<?php

namespace Database\Seeders;

use App\Models\AboutSetting;
use Illuminate\Database\Seeder;

class AboutSeeder extends Seeder
{
    public function run(): void
    {
        $setting = AboutSetting::firstOrNew();
        $setting->hero_image_path = config('about.hero_image_path');
        $setting->profile_image_path ??= config('about.profile_image_path');

        foreach (['tr', 'en'] as $language) {
            foreach (config("about.translations.{$language}") as $field => $value) {
                $setting->setTranslation($field, $language, $value);
            }

            foreach (['hero_panels', 'focus_cards', 'principles'] as $field) {
                $setting->setTranslation($field, $language, config("about.{$field}.{$language}"));
            }
        }

        $setting->save();
    }
}
