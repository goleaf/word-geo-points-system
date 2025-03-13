<?php

use App\Http\Controllers\CitiesController;
use App\Http\Controllers\CountriesController;
use App\Http\Controllers\GeoPointsController;
use App\Http\Controllers\GeocodingController;
use App\Http\Controllers\LanguageManagerController;
use App\Http\Controllers\NameTranslationController;
use App\Http\Controllers\TranslationController;
use Illuminate\Support\Facades\Route;

// Default route redirects to countries
Route::get('/', fn() => redirect()->route('countries.index'));

// Language Change Route
Route::get('/locale/{locale}', [CountriesController::class, 'changeLocale'])->name('locale.change');

// Countries Routes
Route::resource('countries', CountriesController::class)->except(['change-locale']);
Route::get('/countries/change-locale/{locale}', [CountriesController::class, 'changeLocale'])->name('countries.change-locale');

// Cities Routes - Nested under Countries when needed
Route::prefix('countries/{country}/cities')->group(function () {
    Route::get('/', [CitiesController::class, 'indexByCountry'])->name('countries.cities.index');
    Route::get('/create', [CitiesController::class, 'create'])->name('countries.cities.create');
});
Route::resource('cities', CitiesController::class);

// Geo Points Routes - Nested under Cities when needed
Route::prefix('cities/{city}/geo-points')->group(function () {
    Route::get('/', [GeoPointsController::class, 'indexByCity'])->name('cities.geo-points.index');
    Route::get('/create', [GeoPointsController::class, 'create'])->name('cities.geo-points.create');
});
Route::resource('geo-points', GeoPointsController::class)->parameters([
    'geo-points' => 'geo_point'
]);

// Language Manager Routes
Route::resource('language-manager', LanguageManagerController::class)->except(['show'])->parameters(['language-manager' => 'code']);

// Translation routes
Route::post('/translate', [TranslationController::class, 'translate'])->name('translate');
Route::post('/translate-name', [NameTranslationController::class, 'translateName'])->name('translate-name');
Route::post('/translate-all-names', [NameTranslationController::class, 'translateAllNames'])->name('translate-all-names');

// Geocoding routes
Route::post('/geocode', [GeocodingController::class, 'geocode'])->name('geocode');
Route::post('/generate-location-data', [GeocodingController::class, 'generateLocationData'])->name('generate-location-data');
