<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            SiteSetting::KEY_HOME_HERO_TITLE => [
                'lv' => 'Modernas brīvdienu dizaina mājas tavai atpūtai!',
                'en' => 'Modern holiday design houses for your relaxation!',
            ],
        ];

        foreach ($defaults as $key => $translations) {
            if (SiteSetting::where('key', $key)->exists()) {
                continue;
            }

            $setting = new SiteSetting(['key' => $key]);
            $setting->setTranslations('value', $translations);
            $setting->save();
        }
    }
}
