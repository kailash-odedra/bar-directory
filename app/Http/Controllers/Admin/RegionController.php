<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRegionRequest;
use App\Http\Requests\Admin\UpdateRegionRequest;
use App\Models\City;
use App\Models\Region;
use App\Models\State;
use App\Support\GeneratesSlugs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class RegionController extends Controller
{
    use GeneratesSlugs;

    public function index(Request $request)
    {
        // Cache dropdown data (changes infrequently)
        $cities = Cache::remember('cities.for_dropdown', 3600, function() {
            return City::select('id', 'name', 'state_id')
                ->with('state:id,name,country_id')
                ->orderBy('name')
                ->get();
        });
        
        $states = Cache::remember('states.for_dropdown', 3600, function() {
            return State::select('id', 'name', 'country_id')
                ->with('country:id,name')
                ->orderBy('name')
                ->get();
        });

        // Optimize: Select only needed columns
        $regions = Region::select('regions.id', 'regions.name', 'regions.slug', 'regions.city_id', 'regions.is_active', 'regions.created_at')
            ->with(['city:id,name,state_id', 'city.state:id,name,country_id', 'city.state.country:id,name'])
            ->when($request->filled('city_id'), fn ($query) => $query->where('city_id', $request->city_id))
            ->when($request->filled('state_id'), function ($query) use ($request) {
                $query->whereHas('city', fn ($cityQuery) => $cityQuery->where('state_id', $request->state_id));
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

        return view('admin.geo.regions.index', [
            'regions' => $regions,
            'cities' => $cities,
            'states' => $states,
            'filters' => $request->only('city_id', 'state_id', 'search', 'is_active'),
            'title' => 'Regions List',
            'catName' => 'geo',
            'subCatName' => 'regions',
            'scrollspy' => false,
            'simplePage' => false,
        ]);
    }

    public function create()
    {
        $cities = City::with('state.country')->orderBy('name')->get();

        return view('admin.geo.regions.create', [
            'cities' => $cities,
            'title' => 'Add New Region',
            'catName' => 'geo',
            'subCatName' => 'regions',
            'scrollspy' => false,
            'simplePage' => false,
        ]);
    }

    public function store(StoreRegionRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = $this->makeUniqueSlug($data['slug'] ?? null, $data['name'], Region::class);
        $data['is_active'] = $data['is_active'] ?? true;

        Region::create($data);

        return redirect()
            ->route('admin.regions.index')
            ->with('success', 'Region created successfully.');
    }

    public function edit(Region $region)
    {
        $cities = City::with('state.country')->orderBy('name')->get();

        return view('admin.geo.regions.create', [ // reuse create view
            'region' => $region,
            'cities' => $cities,
            'title' => 'Edit Region',
            'catName' => 'geo',
            'subCatName' => 'regions',
            'scrollspy' => false,
            'simplePage' => false,
        ]);
    }

    public function update(UpdateRegionRequest $request, Region $region)
    {
        $data = $request->validated();
        $data['slug'] = $this->makeUniqueSlug($data['slug'] ?? $region->slug, $data['name'], Region::class, $region->id);
        $data['is_active'] = $data['is_active'] ?? true;

        $region->update($data);

        return redirect()
            ->route('admin.regions.index')
            ->with('success', 'Region updated successfully.');
    }

    public function toggleStatus(Region $region)
    {
        $region->is_active = $region->is_active == 1 ? 0 : 1;
        $region->save();
        return response()->json([
            'success' => true,
            'status' => $region->is_active
        ]);
    }

    public function destroy(Region $region)
    {
        $region->delete();

        return redirect()
            ->route('admin.regions.index')
            ->with('success', 'Region deleted.');
    }
}

