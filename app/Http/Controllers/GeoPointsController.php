<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\GeoPoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class GeoPointsController extends Controller
{
    /**
     * Display a listing of the geo points.
     */
    public function index(Request $request)
    {
        $currentLocale = app('currentLocale');

        $search = $request->query('search', '');
        $sortField = $request->query('sortField', 'name');
        $sortDirection = $request->query('sortDirection', 'asc');
        $cityId = $request->query('city_id');
        $city = null;

        $nameField = "name_{$currentLocale}";

        $query = GeoPoint::query();

        if ($cityId) {
            $query->where('city_id', $cityId);
            $city = City::findOrFail($cityId);
        }

        if (!empty($search)) {
            $query->where($nameField, 'like', '%' . $search . '%');
        }

        if ($sortField === 'name') {
            $query->orderBy($nameField, $sortDirection);
        } else if ($sortField === 'city_id') {
            $query->join('cities', 'geo_points.city_id', '=', 'cities.id')
                ->orderBy("cities.city_name_{$currentLocale}", $sortDirection)
                ->select('geo_points.*');
        } else {
            $query->orderBy($sortField, $sortDirection);
        }

        $geoPoints = $query->with('city.country')->paginate(env('PAGINATION_ITEMS', 20))->withQueryString();
        $cities = City::with('country')->orderBy("city_name_{$currentLocale}")->get();

        return view('geo-points.index', compact(
            'geoPoints',
            'cities',
            'city',
            'search',
            'sortField',
            'sortDirection'
        ));
    }

    /**
     * Display a listing of the geo points for a specific city.
     */
    public function indexByCity(Request $request, City $city)
    {
        $request->merge(['city_id' => $city->id]);
        return $this->index($request);
    }

    /**
     * Show the form for creating a new geo point.
     */
    public function create(Request $request, City $city = null)
    {
        $currentLocale = app('currentLocale');
        $availableLocales = array_keys(config('languages.languages') ?? []);

        // Get city_id from URL or from the City model
        $cityId = $city ? $city->id : $request->query('city_id');

        // If city_id is provided, load the city for display
        $selectedCity = null;
        if ($cityId) {
            $selectedCity = City::with('country')->find($cityId);
        }

        $cities = City::with('country')->orderBy("city_name_{$currentLocale}")->get();

        $validationRules = [];
        foreach ($availableLocales as $locale) {
            if (isset(config('languages.languages')[$locale]['validation'])) {
                $validationRules[$locale] = config('languages.languages')[$locale]['validation'];
            }
        }

        return view('geo-points.form', compact(
            'cities',
            'cityId',
            'currentLocale',
            'validationRules',
            'availableLocales',
            'selectedCity'
        ));
    }

    /**
     * Store a newly created geo point in storage.
     */
    public function store(Request $request)
    {
        $currentLocale = app('currentLocale');
        $availableLocales = array_keys(config('languages.languages') ?? []);

        $rules = [
            'city_id' => 'required|exists:cities,id',
            'lat' => 'required|numeric|between:-90,90',
            'long' => 'required|numeric|between:-180,180',
        ];

        foreach ($availableLocales as $locale) {
            $rules["name_{$locale}"] = $locale === config('languages.default')
                ? 'required|string|max:255'
                : 'nullable|string|max:255';
            $rules["description_{$locale}"] = 'nullable|string';
        }

        $validatedData = $request->validate($rules);

        GeoPoint::create($validatedData);

        return redirect()->route('geo-points.index', ['city_id' => $request->city_id, 'currentLocale' => $currentLocale])
            ->with('message', 'Geo point created successfully.');
    }

    /**
     * Display the specified geo point.
     */
    public function show(Request $request, GeoPoint $geoPoint)
    {
        $currentLocale = app('currentLocale');

        return view('geo-points.show', compact('geoPoint'));
    }

    /**
     * Show the form for editing the specified geo point.
     */
    public function edit(Request $request, GeoPoint $geoPoint)
    {
        $currentLocale = app('currentLocale');
        $availableLocales = array_keys(config('languages.languages') ?? []);

        $cities = City::with('country')->orderBy("city_name_{$currentLocale}")->get();

        $validationRules = [];
        foreach ($availableLocales as $locale) {
            if (isset(config('languages.languages')[$locale]['validation'])) {
                $validationRules[$locale] = config('languages.languages')[$locale]['validation'];
            }
        }

        return view('geo-points.form', compact('geoPoint', 'cities', 'currentLocale', 'validationRules', 'availableLocales'));
    }

    /**
     * Update the specified geo point in storage.
     */
    public function update(Request $request, GeoPoint $geoPoint)
    {
        $currentLocale = app('currentLocale');
        $availableLocales = array_keys(config('languages.languages') ?? []);

        $rules = [
            'city_id' => 'required|exists:cities,id',
            'lat' => 'required|numeric|between:-90,90',
            'long' => 'required|numeric|between:-180,180',
        ];

        foreach ($availableLocales as $locale) {
            $rules["name_{$locale}"] = $locale === config('languages.default')
                ? 'required|string|max:255'
                : 'nullable|string|max:255';
            $rules["description_{$locale}"] = 'nullable|string';
        }

        $validatedData = $request->validate($rules);

        $geoPoint->update($validatedData);

        return redirect()->route('geo-points.index', ['city_id' => $geoPoint->city_id, 'currentLocale' => $currentLocale])
            ->with('message', 'Geo point updated successfully.');
    }

    /**
     * Remove the specified geo point from storage.
     */
    public function destroy(Request $request, GeoPoint $geoPoint)
    {
        $currentLocale = app('currentLocale');

        $cityId = $geoPoint->city_id;
        $geoPoint->delete();

        return redirect()->route('geo-points.index', ['city_id' => $cityId, 'currentLocale' => $currentLocale])
            ->with('message', 'Geo point deleted successfully.');
    }
}
