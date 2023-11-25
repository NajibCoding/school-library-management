<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Title Website
        Setting::updateOrCreate(
            ['id' => 1],
            [
                'name' => 'title_website',
                'type' => 'input',
                'autoload' => 'yes',
            ]
        );

        // Description Website on meta tag
        Setting::updateOrCreate(
            ['id' => 2],
            [
                'name' => 'description_website',
                'type' => 'input',
                'autoload' => 'yes',
            ]
        );

        // Favicon Website
        Setting::updateOrCreate(
            ['id' => 3],
            [
                'name' => 'favicon_website',
                'type' => 'file',
                'autoload' => 'yes',
                'file_allowed_mimes' => 'jpg,jpeg,png,gif,webp,ico',
                'file_allowed_max_size' => '3072' //3Mebibita
            ]
        );

        // Apple Touch Favicon Website
        Setting::updateOrCreate(
            ['id' => 4],
            [
                'name' => 'apple_touch_favicon_website',
                'type' => 'file',
                'autoload' => 'yes',
                'file_allowed_mimes' => 'jpg,jpeg,png,gif,webp,ico',
                'file_allowed_max_size' => '3072' //3Mebibita
            ]
        );

        // Logo Website
        Setting::updateOrCreate(
            ['id' => 5],
            [
                'name' => 'logo_website',
                'type' => 'file',
                'autoload' => 'yes',
                'file_allowed_mimes' => 'jpg,jpeg,png,gif,webp',
                'file_allowed_max_size' => '5120' //5Mebibita
            ]
        );

        // Keywords Website
        Setting::updateOrCreate(
            ['id' => 6],
            [
                'name' => 'keywords_website',
                'type' => 'input',
                'autoload' => 'yes',
            ]
        );
    }
}
