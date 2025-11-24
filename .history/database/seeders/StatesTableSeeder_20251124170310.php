<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\State;

class StatesTableSeeder extends Seeder
{
    public function run()
    {
        // India example states
        $india = Country::where('iso2','IN')->first();
        if ($india) {
            $indStates = ['Maharashtra','Karnataka','Delhi','Tamil Nadu','Gujarat'];
            foreach ($indStates as $s) {
                State::updateOrCreate(
                    ['country_id' => $india->id, 'name' => $s],
                    ['country_id' => $india->id, 'name' => $s]
                );
            }
        }

        // US example states
        $us = Country::where('iso2','US')->first();
        if ($us) {
            $usStates = ['California','Texas','New York','Florida','Illinois'];
            foreach ($usStates as $s) {
                State::updateOrCreate(
                    ['country_id' => $us->id, 'name' => $s],
                    ['country_id' => $us->id, 'name' => $s]
                );
            }
        }

        // UK example (as "countries" sometimes have regions)
        $uk = Country::where('iso2','GB')->first();
        if ($uk) {
            $ukStates = ['England','Scotland','Wales','Northern Ireland'];
            foreach ($ukStates as $s) {
                State::updateOrCreate(
                    ['country_id' => $uk->id, 'name' => $s],
                    ['country_id' => $uk->id, 'name' => $s]
                );
            }
        }
    }
}
