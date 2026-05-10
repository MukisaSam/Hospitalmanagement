<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'hospital_name',        'value' => 'Makerere University Hospital',         'type' => 'string'],
            ['key' => 'hospital_address',      'value' => 'Makerere Hill Road, Kampala, Uganda',  'type' => 'string'],
            ['key' => 'hospital_phone',        'value' => '+256-414-530-000',                     'type' => 'string'],
            ['key' => 'hospital_email',        'value' => 'info@mak.ac.ug',                       'type' => 'string'],
            ['key' => 'hospital_logo',         'value' => null,                                   'type' => 'file'],
            ['key' => 'timezone',              'value' => 'Africa/Kampala',                       'type' => 'string'],
            ['key' => 'currency',              'value' => 'UGX',                                  'type' => 'string'],
            ['key' => 'tax_rate',              'value' => '18',                                   'type' => 'integer'],
            ['key' => 'show_notes_to_patient', 'value' => '0',                                    'type' => 'boolean'],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value'], 'type' => $setting['type']]
            );
        }
    }
}
