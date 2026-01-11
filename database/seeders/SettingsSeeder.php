<?php

namespace Database\Seeders;

use App\Models\Site\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    use WithoutModelEvents;

    private $settings = [
        ['maintenance', 0, 'The site is currently under maintenance. Sorry.'],
        ['open_reg', 1, 'Registration is currently closed.'],
        ['invite_gen', 0, 'Enable to allow users to generate invite codes.'],
    ];

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        foreach ($this->settings as $setting) {
            Setting::create([
                'ref_key' => $setting[0],
                'value' => $setting[1],
                'text' => $setting[2]
            ]);
        }
    }
}
