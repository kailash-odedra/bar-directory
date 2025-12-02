<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\State;
use App\Models\City;
use App\Models\Region;
use App\Models\Country;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Return states for a given country as JSON.
     * GET /admin/get-states/{countryId}
     */
    public function states($countryId)
    {
        // optional: return empty array if countryId is missing
        if (! $countryId) {
            return response()->json([], 200);
        }

        $states = State::where('country_id', $countryId)
            ->orderBy('name')
            ->get(['id','name']);

        return response()->json($states, 200);
    }

    /**
     * Return cities for a given state as JSON.
     * GET /admin/get-cities/{stateId}
     */
    public function cities($stateId)
    {
        // optional: return empty array if stateId is missing
        if (! $stateId) {
            return response()->json([], 200);
        }

        $cities = City::where('state_id', $stateId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id','name']);

        return response()->json($cities, 200);
    }

    /**
     * Return regions for a given city as JSON.
     * GET /admin/get-regions/{cityId}
     */
    public function regions($cityId)
    {
        // optional: return empty array if cityId is missing
        if (! $cityId) {
            return response()->json([], 200);
        }

        $regions = Region::where('city_id', $cityId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id','name']);

        return response()->json($regions, 200);
    }
}
