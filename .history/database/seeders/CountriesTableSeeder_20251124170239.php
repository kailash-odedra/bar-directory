<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;

class CountriesTableSeeder extends Seeder
{
    public function run()
    {
        $countries = [
            ['name' => 'India', 'iso2' => 'IN'],
            ['name' => 'United States', 'iso2' => 'US'],
            ['name' => 'United Kingdom', 'iso2' => 'GB'],
            ['name' => 'Australia', 'iso2' => 'AU'],
            ['name' => 'Canada', 'iso2' => 'CA'],
        ];

        foreach ($countries as $c) {
            Country::updateOrCreate(
                ['iso2' => $c['iso2']],
                ['name' => $c['name'], 'iso2' => $c['iso2']]
            );
        }
    }
}
