<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class CitiesController extends Controller
{
    /**
     * Display a listing of the cities.
     */
    public function index(Request $request)
    {
        $currentLocale = app('currentLocale');

        $search = $request->query('search', '');
        $sortField = $request->query('sortField', 'city_name');
        $sortDirection = $request->query('sortDirection', 'asc');
        $countryId = $request->query('country_id');
        $country = null;

        $nameField = "city_name_{$currentLocale}";

        $query = City::query();

        if ($countryId) {
            $query->where('country_id', $countryId);
            $country = Country::findOrFail($countryId);
        }

        if (!empty($search)) {
            $query->where($nameField, 'like', '%' . $search . '%');
        }

        if ($sortField === 'city_name') {
            $query->orderBy($nameField, $sortDirection);
        } else if ($sortField === 'country_id') {
            $query->join('countries', 'cities.country_id', '=', 'countries.id')
                ->orderBy("countries.name_{$currentLocale}", $sortDirection)
                ->select('cities.*');
        } else if ($sortField === 'geo_points_count') {
            $query->withCount('geoPoints')->orderBy('geo_points_count', $sortDirection);
        } else {
            $query->orderBy($sortField, $sortDirection);
        }

        $cities = $query->with('country')->withCount('geoPoints')->paginate(env('PAGINATION_ITEMS', 20))->withQueryString();
        $countries = Country::orderBy("name_{$currentLocale}")->get();

        return view('cities.index', compact(
            'cities',
            'countries',
            'search',
            'sortField',
            'sortDirection',
            'countryId',
            'country'
        ));
    }

    /**
     * Display a listing of the cities for a specific country.
     */
    public function indexByCountry(Request $request, Country $country)
    {
        $request->merge(['country_id' => $country->id]);
        return $this->index($request);
    }

    /**
     * Show the form for creating a new city.
     */
    public function create(Request $request, Country $country = null)
    {
        $currentLocale = app('currentLocale');
        $availableLocales = array_keys(config('languages.languages') ?? []);

        // Get country_id from URL or from the Country model
        $countryId = $country ? $country->id : $request->query('country_id');

        // If country_id is provided, load the country for display
        $selectedCountry = null;
        if ($countryId) {
            $selectedCountry = Country::find($countryId);
        }

        $countries = Country::orderBy("name_" . $currentLocale)->get();

        $validationRules = [];
        foreach ($availableLocales as $locale) {
            if (isset(config('languages.languages')[$locale]['validation'])) {
                $validationRules[$locale] = config('languages.languages')[$locale]['validation'];
            }
        }

        return view('cities.form', compact(
            'countries',
            'countryId',
            'currentLocale',
            'validationRules',
            'availableLocales',
            'selectedCountry'
        ));
    }

    /**
     * Store a newly created city in storage.
     */
    public function store(Request $request)
    {
        $currentLocale = app('currentLocale');
        $availableLocales = array_keys(config('languages.languages') ?? []);

        $rules = [
            'country_id' => 'required|exists:countries,id',
        ];

        foreach ($availableLocales as $locale) {
            $rules["city_name_{$locale}"] = $locale === config('languages.default')
                ? 'required|string|max:255'
                : 'nullable|string|max:255';
            $rules["city_description_{$locale}"] = 'nullable|string';
        }

        $validatedData = $request->validate($rules);

        City::create($validatedData);

        return redirect()->route('cities.index', ['country_id' => $request->country_id, 'currentLocale' => $currentLocale])
            ->with('message', 'City created successfully.');
    }

    /**
     * Display the specified city.
     */
    public function show(Request $request, City $city)
    {
        $currentLocale = app('currentLocale');

        $geoPoints = $city->geoPoints()->paginate(env('PAGINATION_ITEMS', 20));

        return view('cities.show', compact('city', 'geoPoints'));
    }

    /**
     * Show the form for editing the specified city.
     */
    public function edit(Request $request, City $city)
    {
        $currentLocale = app('currentLocale');
        $availableLocales = array_keys(config('languages.languages') ?? []);

        $countries = Country::orderBy("name_" . $currentLocale)->get();

        $validationRules = [];
        foreach ($availableLocales as $locale) {
            if (isset(config('languages.languages')[$locale]['validation'])) {
                $validationRules[$locale] = config('languages.languages')[$locale]['validation'];
            }
        }

        return view('cities.form', compact('city', 'countries', 'currentLocale', 'validationRules', 'availableLocales'));
    }

    /**
     * Update the specified city in storage.
     */
    public function update(Request $request, City $city)
    {
        $currentLocale = app('currentLocale');
        $availableLocales = array_keys(config('languages.languages') ?? []);

        $rules = [
            'country_id' => 'required|exists:countries,id',
        ];

        foreach ($availableLocales as $locale) {
            $rules["city_name_{$locale}"] = $locale === config('languages.default')
                ? 'required|string|max:255'
                : 'nullable|string|max:255';
            $rules["city_description_{$locale}"] = 'nullable|string';
        }

        $validatedData = $request->validate($rules);

        $city->update($validatedData);

        return redirect()->route('cities.index', ['country_id' => $city->country_id, 'currentLocale' => $currentLocale])
            ->with('message', 'City updated successfully.');
    }

    /**
     * Remove the specified city from storage.
     */
    public function destroy(Request $request, City $city)
    {
        $currentLocale = app('currentLocale');

        $countryId = $city->country_id;
        $city->delete();

        return redirect()->route('cities.index', ['country_id' => $countryId, 'currentLocale' => $currentLocale])
            ->with('message', 'City deleted successfully.');
    }
}
