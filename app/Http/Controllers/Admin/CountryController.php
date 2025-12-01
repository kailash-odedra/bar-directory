<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCountryRequest;
use App\Http\Requests\Admin\UpdateCountryRequest;
use App\Models\Country;
use App\Support\GeneratesSlugs;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    use GeneratesSlugs;

    public function index(Request $request)
    {
        $countries = Country::query()
            ->withCount('states')
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where(function ($inner) use ($request) {
                    $inner->where('name', 'like', '%'.$request->search.'%')
                        ->orWhere('slug', 'like', '%'.$request->search.'%')
                        ->orWhere('iso_code', 'like', '%'.$request->search.'%');
                });
            })
            ->when($request->filled('is_active'), fn ($query) => $query->where('is_active', $request->is_active))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.geo.countries.index', [
            'countries' => $countries,
            'filters' => $request->only('search', 'is_active'),
            'title' => 'Countries List',
            'catName' => 'geo',
            'subCatName' => 'countries',
            'scrollspy' => false,
            'simplePage' => false,
        ]);
    }

    public function create()
    {
        return view('admin.geo.countries.create', [
            'title' => 'Add New Country',
            'catName' => 'geo',
            'subCatName' => 'countries',
            'scrollspy' => false,
            'simplePage' => false,
        ]);
    }

    public function store(StoreCountryRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = $this->makeUniqueSlug($data['slug'] ?? null, $data['name'], Country::class);
        $data['is_active'] = $data['is_active'] ?? true;

        Country::create($data);

        return redirect()
            ->route('admin.countries.index')
            ->with('success', 'Country created successfully.');
    }

    public function edit(Country $country)
    {
        return view('admin.geo.countries.create', [ // reuse create view
            'country' => $country,
            'title' => 'Edit Country',
            'catName' => 'geo',
            'subCatName' => 'countries',
            'scrollspy' => false,
            'simplePage' => false,
        ]);
    }

    public function update(UpdateCountryRequest $request, Country $country)
    {
        $data = $request->validated();
        $data['slug'] = $this->makeUniqueSlug($data['slug'] ?? $country->slug, $data['name'], Country::class, $country->id);
        $data['is_active'] = $data['is_active'] ?? true;

        $country->update($data);

        return redirect()
            ->route('admin.countries.index')
            ->with('success', 'Country updated successfully.');
    }

    public function toggleStatus(Country $country)
    {
        $country->is_active = $country->is_active == 1 ? 0 : 1;
        $country->save();
        return response()->json([
            'success' => true,
            'status' => $country->is_active
        ]);
    }

    public function destroy(Country $country)
    {
        $country->delete();

        return redirect()
            ->route('admin.countries.index')
            ->with('success', 'Country deleted.');
    }
}

