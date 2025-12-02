<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStateRequest;
use App\Http\Requests\Admin\UpdateStateRequest;
use App\Models\Country;
use App\Models\State;
use App\Support\GeneratesSlugs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class StateController extends Controller
{
    use GeneratesSlugs;

    public function index(Request $request)
    {
        $countries = Country::orderBy('name')->get();

        // Optimize: Select only needed columns
        $states = State::select('states.id', 'states.name', 'states.slug', 'states.country_id', 'states.is_active', 'states.created_at')
            ->with('country:id,name')
            ->when($request->filled('country_id'), fn ($query) => $query->where('country_id', $request->country_id))
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

        return view('admin.geo.states.index', [
            'states' => $states,
            'countries' => $countries,
            'filters' => $request->only('country_id', 'search', 'is_active'),
            'title' => 'States List',
            'catName' => 'geo',
            'subCatName' => 'states',
            'scrollspy' => false,
            'simplePage' => false,
        ]);
    }

    public function create()
    {
        $countries = Country::orderBy('name')->get();

        return view('admin.geo.states.create', [
            'countries' => $countries,
            'title' => 'Add New State',
            'catName' => 'geo',
            'subCatName' => 'states',
            'scrollspy' => false,
            'simplePage' => false,
        ]);
    }

    public function store(StoreStateRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = $this->makeUniqueSlug($data['slug'] ?? null, $data['name'], State::class);
        $data['is_active'] = $data['is_active'] ?? true;

        State::create($data);

        return redirect()
            ->route('admin.states.index')
            ->with('success', 'State created successfully.');
    }

    public function edit(State $state)
    {
        $countries = Country::orderBy('name')->get();

        return view('admin.geo.states.create', [ // reuse create view
            'state' => $state,
            'countries' => $countries,
            'title' => 'Edit State',
            'catName' => 'geo',
            'subCatName' => 'states',
            'scrollspy' => false,
            'simplePage' => false,
        ]);
    }

    public function update(UpdateStateRequest $request, State $state)
    {
        $data = $request->validated();
        $data['slug'] = $this->makeUniqueSlug($data['slug'] ?? $state->slug, $data['name'], State::class, $state->id);
        $data['is_active'] = $data['is_active'] ?? true;

        $state->update($data);

        return redirect()
            ->route('admin.states.index')
            ->with('success', 'State updated successfully.');
    }

    public function toggleStatus(State $state)
    {
        $state->is_active = $state->is_active == 1 ? 0 : 1;
        $state->save();
        return response()->json([
            'success' => true,
            'status' => $state->is_active
        ]);
    }

    public function destroy(State $state)
    {
        $state->delete();

        return redirect()
            ->route('admin.states.index')
            ->with('success', 'State deleted.');
    }
}

