<?php

namespace App\Http\Requests;

use Config;
use Illuminate\Validation\Rule;
use Illuminate\Database\Query\Builder;

use App\Models\Site\Setting;
use App\Validators\PasswordValidationRules;

use Illuminate\Foundation\Http\FormRequest;
class RegisterUserRequest extends FormRequest
{
	use PasswordValidationRules;

	/**
	 * Determine if the user is authorized to make this request.
	 */
	public function authorize(): bool
	{
		if($this->user()) return false; // They're already signed in, so they shouldn't be registering
		return true;
	}

	/**
	 * Prepare the data for validation.
	 */
	protected function prepareForValidation(): void
	{
		$this->merge([
			'open_reg' => Setting::where('ref_key', 'open_reg')->first()->value ? true : false,
		]);
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
	 */
	public function rules(): array
	{
		return [
			'username' => 'required|string|alpha_dash:ascii|unique:users|min:2|max:32',
			'email' => 'required|string|email|max:255',
			'password' => $this->passwordRules(),
			'birthday' => [
				'required',
				'date',
				Rule::date()->beforeOrEqual(today()->subYears(Config::get('site_settings.age_required'))),
				Rule::date()->after(today()->subYears(120))
			],
			'tos' => 'accepted',
			'privacy' => 'accepted',
			'referrer' => 'nullable|exists:users,username',
			'reg_code' => [
				'exclude_if:open_reg,true',
				'required_if:open_reg,false',
				Rule::exists('reg_codes', 'token')->where(function (Builder $query) {
					$query->where('is_used', 0);
				})
			],
		];
	}

	/**
	 * Get the error messages for the defined validation rules.
	 *
	 * @return array<string, string>
	 */
	public function messages(): array
	{
		return [
			'birthday.before_or_equal' => 'You are not old enough to register.',
			'birthday.after' => 'Please enter an accurate birthdate.',
			'reg_code.required_if' => 'Registration is currently closed. You must enter a valid registration code to sign up.',
			'reg_code.exists' => 'The provided registration code is invalid or has already been used.',
		];
	}
}
