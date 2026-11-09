<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\Page;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use LogicException;

/**
 * Minimal verified shop details for isolated browser QA only.
 */
class MenuPreviewSeeder extends Seeder
{
    public function run(): void
    {
        $connection = (string) config('database.default');
        $database = strtolower((string) config("database.connections.{$connection}.database"));
        if (! app()->environment(['local', 'staging', 'testing'])
            || ! (str_contains($database, 'staging') || str_contains($database, 'test') || $database === ':memory:')) {
            throw new LogicException('Menu preview seed can run only in an isolated staging or test database.');
        }

        DB::transaction(function (): void {
            Contact::firstOrCreate(
                ['phone1' => '01012716500'],
                [
                    'phone2' => null,
                    'email' => '',
                    'time' => '',
                    'address' => ['ar' => 'مدينة السادات - مول الزهور - المنطقة الخامسة'],
                    'whatsapp' => null,
                ]
            );

            Setting::firstOrCreate(
                ['id' => 1],
                [
                    'keywords' => ['ar' => 'الشامي، مدينة السادات'],
                    'description' => ['ar' => 'قائمة طعام الشامي في مدينة السادات'],
                    'main_image' => '',
                    'banner_image' => '',
                    'banner_url' => '',
                ]
            );

            Page::firstOrCreate(
                ['id' => 1],
                [
                    'title' => ['ar' => 'عن الشامي'],
                    'text' => ['ar' => 'مطعم الشامي في مدينة السادات.'],
                    'image' => '',
                ]
            );
        });
    }
}
