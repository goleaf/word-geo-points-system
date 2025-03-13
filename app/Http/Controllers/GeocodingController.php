<?php

namespace App\Http\Controllers;

use App\Models\GeoPoint;
use App\Services\GeocodingService;
use App\Services\GeoPointDescriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GeocodingController extends Controller
{
    protected $geocodingService;
    protected $descriptionService;

    public function __construct(GeocodingService $geocodingService, GeoPointDescriptionService $descriptionService)
    {
        $this->geocodingService = $geocodingService;
        $this->descriptionService = $descriptionService;
    }

    /**
     * Get coordinates for a geo point based on its name
     */
    public function geocode(Request $request)
    {
        $request->validate([
            'geo_point_id' => 'required|integer',
            'locale' => 'required|string|size:2',
        ]);

        $geoPointId = $request->geo_point_id;
        $locale = $request->locale;
        $currentLocale = app('currentLocale');

        try {
            // Get the geo point
            $geoPoint = GeoPoint::findOrFail($geoPointId);
            $locationName = $geoPoint->{"name_{$locale}"};

            if (empty($locationName)) {
                return response()->json([
                    'error' => "No name available in the selected language ({$locale})"
                ], 400);
            }

            // Get city and country names for context
            $city = $geoPoint->city;
            $cityName = $city ? $city->{"city_name_{$locale}"} : null;
            $countryName = $city && $city->country ? $city->country->{"name_{$locale}"} : null;

            // Get coordinates
            $coordinates = $this->geocodingService->getCoordinates($locationName, $cityName, $countryName);

            if ($coordinates === null) {
                return response()->json(['error' => 'Geocoding failed'], 500);
            }

            // Update the geo point with the coordinates
            // Ensure coordinates are stored with 7 decimal places
            $geoPoint->lat = number_format((float)$coordinates['lat'], 7, '.', '');
            $geoPoint->long = number_format((float)$coordinates['long'], 7, '.', '');
            $geoPoint->save();

            // Generate description if it doesn't exist
            $descriptionField = "description_{$locale}";
            if (empty($geoPoint->{$descriptionField})) {
                $description = $this->descriptionService->generateDescription($locationName, $locale, $cityName, $countryName);
                if ($description) {
                    $geoPoint->{$descriptionField} = $description;
                    $geoPoint->save();
                }
            }

            $redirectRoute = route('geo-points.edit', ['geo_point' => $geoPoint, 'currentLocale' => $currentLocale]);

            return response()->json([
                'success' => true,
                'message' => 'Coordinates updated successfully',
                'coordinates' => [
                    'lat' => $geoPoint->lat,
                    'long' => $geoPoint->long
                ],
                'redirect' => $redirectRoute
            ]);
        } catch (\Exception $e) {
            Log::error('Geocoding error: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred during geocoding'], 500);
        }
    }

    /**
     * Generate location data from a place name and address
     */
    public function generateLocationData(Request $request)
    {
        $request->validate([
            'place_name' => 'required|string|max:255',
            'address' => 'required|string',
            'city_id' => 'required|integer',
            'locale' => 'required|string|size:2',
        ]);

        $placeName = $request->place_name;
        $address = $request->address;
        $cityId = $request->city_id;
        $locale = $request->locale;

        try {
            // Get city and country for context
            $city = \App\Models\City::with('country')->findOrFail($cityId);
            $cityName = $city->{"city_name_{$locale}"};
            $countryName = $city->country->{"name_{$locale}"};

            // Create a combined location string for better geocoding
            $locationString = "{$placeName}, {$address}, {$cityName}, {$countryName}";

            // Get coordinates
            $coordinates = $this->geocodingService->getCoordinates($locationString, $cityName, $countryName);

            if ($coordinates === null) {
                return response()->json(['error' => 'Geocoding failed'], 500);
            }

            // Ensure coordinates have 7 decimal places
            $formattedCoordinates = [
                'lat' => number_format((float)$coordinates['lat'], 7, '.', ''),
                'long' => number_format((float)$coordinates['long'], 7, '.', '')
            ];

            // Generate description using the new service
            $description = $this->descriptionService->generateDescription($placeName, $locale, $cityName, $countryName);

            // Get language requirements for the modal
            $requirements = $this->descriptionService->getLanguageRequirements($locale);
            $wordCount = $description ? $this->descriptionService->countWords($description) : 0;

            return response()->json([
                'success' => true,
                'data' => [
                    'name' => $placeName,
                    'coordinates' => $formattedCoordinates,
                    'description' => $description,
                    'requirements' => $requirements,
                    'word_count' => $wordCount
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Location data generation error: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while generating location data'], 500);
        }
    }
}
