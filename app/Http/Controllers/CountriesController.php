<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class CountriesController extends Controller
{
    /**
     * Display a listing of the countries.
     */
    public function index(Request $request)
    {
        $currentLocale = app('currentLocale');

        $search = $request->query('search', '');
        $sortField = $request->query('sortField', 'name');
        $sortDirection = $request->query('sortDirection', 'asc');

        $nameField = "name_{$currentLocale}";

        $query = Country::query();

        if (!empty($search)) {
            $query->where($nameField, 'like', '%' . $search . '%');
        }

        if ($sortField === 'name') {
            $query->orderBy($nameField, $sortDirection);
        } else {
            $query->orderBy($sortField, $sortDirection);
        }

        $countries = $query->withCount('cities')->paginate(env('PAGINATION_ITEMS', 20))->withQueryString();

        return view('countries.index', compact(
            'countries',
            'search',
            'sortField',
            'sortDirection'
        ));
    }

    /**
     * Show the form for creating a new country.
     */
    public function create(Request $request)
    {
        $currentLocale = app('currentLocale');

        $availableLocales = array_keys(config('languages.languages') ?? []);
        $validationRules = [];

        foreach ($availableLocales as $locale) {
            if (isset(config('languages.languages')[$locale]['validation'])) {
                $validationRules[$locale] = config('languages.languages')[$locale]['validation'];
            }
        }

        return view('countries.form', compact('currentLocale', 'validationRules', 'availableLocales'));
    }

    /**
     * Store a newly created country in storage.
     */
    public function store(Request $request)
    {
        $currentLocale = app('currentLocale');
        $availableLocales = array_keys(config('languages.languages') ?? []);

        $rules = [];

        foreach ($availableLocales as $locale) {
            $rules["name_{$locale}"] = $locale === config('languages.default')
                ? 'required|string|max:255'
                : 'nullable|string|max:255';
            $rules["description_{$locale}"] = 'nullable|string';
        }

        $validatedData = $request->validate($rules);

        Country::create($validatedData);

        return redirect()->route('countries.index', ['currentLocale' => $currentLocale])
            ->with('message', 'Country created successfully.');
    }

    /**
     * Show the form for editing the specified country.
     */
    public function edit(Request $request, Country $country)
    {
        $currentLocale = app('currentLocale');

        $availableLocales = array_keys(config('languages.languages') ?? []);
        $validationRules = [];

        foreach ($availableLocales as $locale) {
            if (isset(config('languages.languages')[$locale]['validation'])) {
                $validationRules[$locale] = config('languages.languages')[$locale]['validation'];
            }
        }

        return view('countries.form', compact('country', 'currentLocale', 'validationRules', 'availableLocales'));
    }

    /**
     * Update the specified country in storage.
     */
    public function update(Request $request, Country $country)
    {
        $currentLocale = app('currentLocale');
        $availableLocales = array_keys(config('languages.languages') ?? []);

        $rules = [];

        foreach ($availableLocales as $locale) {
            $rules["name_{$locale}"] = $locale === config('languages.default')
                ? 'required|string|max:255'
                : 'nullable|string|max:255';
            $rules["description_{$locale}"] = 'nullable|string';
        }

        $validatedData = $request->validate($rules);

        $country->update($validatedData);

        return redirect()->route('countries.index', ['currentLocale' => $currentLocale])
            ->with('message', 'Country updated successfully.');
    }

    /**
     * Remove the specified country from storage.
     */
    public function destroy(Request $request, Country $country)
    {
        $currentLocale = app('currentLocale');

        $country->delete();

        return redirect()->route('countries.index', ['currentLocale' => $currentLocale])
            ->with('message', 'Country deleted successfully.');
    }

    /**
     * Change the application locale.
     */
    public function changeLocale(Request $request, $locale)
    {
        // Validate locale
        $availableLocales = array_keys(config('languages.languages') ?? []);
        if (!in_array($locale, $availableLocales)) {
            $locale = config('languages.default');
        }

        // Store locale in session
        session(['currentLocale' => $locale]);

        // Redirect to specified URL or back to previous page
        if ($request->has('redirect')) {
            return redirect($request->redirect);
        }

        return redirect()->back()->withInput();
    }

    /**
     * Display the specified country.
     */
    public function show(Request $request, Country $country)
    {
        $currentLocale = app('currentLocale');

        $search = $request->query('search', '');
        $sortField = $request->query('sortField', 'city_name');
        $sortDirection = $request->query('sortDirection', 'asc');

        $nameField = "city_name_{$currentLocale}";

        $query = $country->cities();

        if (!empty($search)) {
            $query->where($nameField, 'like', '%' . $search . '%');
        }

        if ($sortField === 'city_name') {
            $query->orderBy($nameField, $sortDirection);
        } else if ($sortField === 'geo_points_count') {
            $query->withCount('geoPoints')->orderBy('geo_points_count', $sortDirection);
        } else {
            $query->orderBy($sortField, $sortDirection);
        }

        // Paginate the results
        $cities = $query->withCount('geoPoints')->paginate(env('PAGINATION_ITEMS', 20))->withQueryString();

        return view('countries.show', compact(
            'country',
            'cities',
            'search',
            'sortField',
            'sortDirection'
        ));
    }
}
