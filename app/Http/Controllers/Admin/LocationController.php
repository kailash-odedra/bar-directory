<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\State;
use App\Models\Country;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Return states for a given country as JSON.
     * GET /admin/get-states/{country}
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
}
