<?php

namespace App\Actions\Fortify;

use App\Models\User\User;
use App\Models\User\UserSettings;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Query\Builder;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

use DB;
use Config;
use App\Models\Site\Setting;
use App\Models\Site\RegCode;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        $username = $input['username'];
        $input['username'] = strtolower($input['username']);


        // $blocked_names = Setting::where('ref_key', 'name_blacklist')->first()->value; // Update this to go with the new word blacklist filter and things.

        $open_reg = Setting::where('ref_key', 'open_reg')->first()->value;
        $input['open_reg'] = $open_reg;



        Validator::make($input, [
            'username' => ['required', 'string', 'max:32', 'min:2', Rule::unique(User::class), 'alpha_dash:ascii', 'doesnt_start_with:owner-,admin-,mod-,staff-,coder-',
                // Rule::notIn(explode(',', $blocked_names))
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(UserSettings::class),
            ],
            'password' => $this->passwordRules(),
            'birthday' => [
                'required',
                'date',
                Rule::date()->beforeOrEqual(today()->subYears(Config::get('site_settings.age_required'))),
                Rule::date()->after(today()->subYears(120))
            ],
            'tos' => 'accepted',
            'privacy' => 'accepted',
            'referrer' => ['nullable', 'exists:users,username'],
            'reg_code' => ['required_if_declined:open_reg', Rule::exists('reg_codes', 'token')->where(function (Builder $query) {
                $query->where('is_used', 0);
            })]
        ], $messages = [
            'before_or_equal' => 'You are not old enough to register.',
            'after' => 'Please enter an accurate birth year.',
            'reg_code.required_if_declined' => 'Enter a valid registration code to sign up.',
            'reg_code.exists' => 'The code is invalid or has already been used.'
        ])->validate();





        DB::beginTransaction();

        try {
            $user = User::create([
                'username' => $username,
                'pri_curr' => Config::get('site_settings.pri_start'),
                'sec_curr' => Config::get('site_settings.sec_start'),
            ]);
            $user->settings()->create([
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
                'birthday' => $input['birthday'],
            ]);
            $user->stats()->create([]);
            $user->profile()->create([]);


            if(!$open_reg) {
                $invite = RegCode::where('token', $input['reg_code'])->first();
                $invite->update([
                    'is_used' => 1,
                    'user_id' => $user->id
                ]);
            }


            if(array_key_exists('referrer', $input) && $input['referrer'] != null) {
                $referrer = User::select('username', 'id')->where('username', $input['referrer'])->first();
                $referrer->stats()->update(['referrals' => $referrer->stats->referrals + 1]);

                $user->settings()->update(['was_referred' => 1]);
                $user->referrer()->create(['referred_by' => $referrer->id]);
            }

            DB::commit();
            return $user;
        } catch (Exception $e) {
            DB::rollback();
            // redirect back :)
        }








        return $user;
    }
}
