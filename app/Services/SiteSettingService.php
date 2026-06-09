<?php

namespace App\Services;

use App\Models\SiteSetting;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

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

    /**
     * Store the value for the active locale only, preserving translations for other locales.
     * This mirrors how translatable models (e.g. Product) are edited one language at a time.
     */
    public function setForCurrentLocale(string $key, string $value): SiteSetting
    {
        $setting = SiteSetting::firstOrNew(['key' => $key]);
        $setting->setTranslation('value', app()->getLocale(), $value);
        $setting->save();

        return $setting;
    }

    /**
     * Store the same value for every supported locale.
     * Used for language-agnostic values (e.g. an image path) so they resolve in any locale.
     */
    public function setForAllLocales(string $key, string $value): SiteSetting
    {
        $setting = SiteSetting::firstOrNew(['key' => $key]);

        foreach (LaravelLocalization::getSupportedLanguagesKeys() as $locale) {
            $setting->setTranslation('value', $locale, $value);
        }

        $setting->save();

        return $setting;
    }
}
