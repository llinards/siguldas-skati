<?php

namespace App\Services;

use App\Models\SiteSetting;

class SiteSettingService
{
    public function get(string $key, ?string $default = null): ?string
    {
        $setting = SiteSetting::where('key', $key)->first();

        if (! $setting) {
            return $default;
        }

        $value = $setting->value;

        return $value !== null && $value !== '' ? $value : $default;
    }

    public function getTranslations(string $key): array
    {
        $setting = SiteSetting::where('key', $key)->first();

        if (! $setting) {
            return [];
        }

        return $setting->getTranslations('value');
    }

    public function set(string $key, array $translations): SiteSetting
    {
        $setting = SiteSetting::firstOrNew(['key' => $key]);
        $setting->setTranslations('value', $translations);
        $setting->save();

        return $setting;
    }
}
