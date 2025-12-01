<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCityRequest;
use App\Http\Requests\Admin\UpdateCityRequest;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use App\Support\GeneratesSlugs;
use Illuminate\Http\Request;

class CityController extends Controller
{
    use GeneratesSlugs;

    public function index(Request $request)
    {
        $states = State::with('country')->orderBy('name')->get();
        $countries = Country::orderBy('name')->get();

        $cities = City::query()
            ->with(['state.country'])
            ->when($request->filled('state_id'), fn ($query) => $query->where('state_id', $request->state_id))
            ->when($request->filled('country_id'), function ($query) use ($request) {
                $query->whereHas('state', fn ($stateQuery) => $stateQuery->where('country_id', $request->country_id));
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where(function ($inner) use ($request) {
                    $inner->where('name', 'like', '%'.$request->search.'%')
                        ->orWhere('slug', 'like', '%'.$request->search.'%');
                });
            })
            ->when($request->filled('is_active'), fn ($query) => $query->where('is_active', $request->is_active))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.geo.cities.index', [
            'cities' => $cities,
            'states' => $states,
            'countries' => $countries,
            'filters' => $request->only('state_id', 'country_id', 'search', 'is_active'),
            'title' => 'Cities List',
            'catName' => 'geo',
            'subCatName' => 'cities',
            'scrollspy' => false,
            'simplePage' => false,
        ]);
    }

    public function create()
    {
        $states = State::with('country')->orderBy('name')->get();

        return view('admin.geo.cities.create', [
            'states' => $states,
            'title' => 'Add New City',
            'catName' => 'geo',
            'subCatName' => 'cities',
            'scrollspy' => false,
            'simplePage' => false,
        ]);
    }

    public function store(StoreCityRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = $this->makeUniqueSlug($data['slug'] ?? null, $data['name'], City::class);
        $data['is_active'] = $data['is_active'] ?? true;

        City::create($data);

        return redirect()
            ->route('admin.cities.index')
            ->with('success', 'City created successfully.');
    }

    public function edit(City $city)
    {
        $states = State::with('country')->orderBy('name')->get();

        return view('admin.geo.cities.create', [ // reuse create view
            'city' => $city,
            'states' => $states,
            'title' => 'Edit City',
            'catName' => 'geo',
            'subCatName' => 'cities',
            'scrollspy' => false,
            'simplePage' => false,
        ]);
    }

    public function update(UpdateCityRequest $request, City $city)
    {
        $data = $request->validated();
        $data['slug'] = $this->makeUniqueSlug($data['slug'] ?? $city->slug, $data['name'], City::class, $city->id);
        $data['is_active'] = $data['is_active'] ?? true;

        $city->update($data);

        return redirect()
            ->route('admin.cities.index')
            ->with('success', 'City updated successfully.');
    }

    public function toggleStatus(City $city)
    {
        $city->is_active = $city->is_active == 1 ? 0 : 1;
        $city->save();
        return response()->json([
            'success' => true,
            'status' => $city->is_active
        ]);
    }

    public function destroy(City $city)
    {
        $city->delete();

        return redirect()
            ->route('admin.cities.index')
            ->with('success', 'City deleted.');
    }
}

