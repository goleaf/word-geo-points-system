<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Country;
use App\Models\GeoPoint;
use App\Services\TranslationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TranslationController extends Controller
{
    protected $translationService;

    public function __construct(TranslationService $translationService)
    {
        $this->translationService = $translationService;
    }

    /**
     * Translate entity description to the target language
     */
    public function translate(Request $request)
    {
        $request->validate([
            'entity_type' => 'required|string|in:country,city,geo_point',
            'entity_id' => 'required|integer',
            'source_locale' => 'required|string|size:2',
            'target_locale' => 'required|string|size:2',
        ]);

        $entityType = $request->entity_type;
        $entityId = $request->entity_id;
        $sourceLocale = $request->source_locale;
        $targetLocale = $request->target_locale;
        $currentLocale = app('currentLocale');

        try {
            // Get the entity and its description based on entity type
            switch ($entityType) {
                case 'country':
                    $entity = Country::findOrFail($entityId);
                    $sourceText = $entity->{"description_{$sourceLocale}"};
                    $entityName = $entity->{"name_{$sourceLocale}"};
                    $descriptionField = "description_{$targetLocale}";
                    break;
                case 'city':
                    $entity = City::findOrFail($entityId);
                    $sourceText = $entity->{"city_description_{$sourceLocale}"};
                    $entityName = $entity->{"city_name_{$sourceLocale}"};
                    $descriptionField = "city_description_{$targetLocale}";
                    break;
                case 'geo_point':
                    $entity = GeoPoint::findOrFail($entityId);
                    $sourceText = $entity->{"description_{$sourceLocale}"};
                    $entityName = $entity->{"name_{$sourceLocale}"};
                    $descriptionField = "description_{$targetLocale}";
                    break;
                default:
                    return response()->json(['error' => 'Invalid entity type'], 400);
            }

            // Check if source text exists
            if (empty($sourceText)) {
                return response()->json([
                    'error' => "No description available in the source language ({$sourceLocale})"
                ], 400);
            }

            // Translate the text
            $translatedText = $this->translationService->translate(
                $sourceText,
                $sourceLocale,
                $targetLocale,
                $entityType,
                $entityName
            );

            if ($translatedText === null) {
                return response()->json(['error' => 'Translation failed'], 500);
            }

            // Update the entity with the translated text
            $entity->{$descriptionField} = $translatedText;
            $entity->save();

            // Determine redirect route based on entity type
            switch ($entityType) {
                case 'country':
                    $redirectRoute = route('countries.edit', ['country' => $entity, 'currentLocale' => $currentLocale]);
                    break;
                case 'city':
                    $redirectRoute = route('cities.edit', ['city' => $entity, 'currentLocale' => $currentLocale]);
                    break;
                case 'geo_point':
                    $redirectRoute = route('geo-points.edit', ['geo_point' => $entity, 'currentLocale' => $currentLocale]);
                    break;
                default:
                    $redirectRoute = route('countries.index', ['currentLocale' => $currentLocale]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Translation completed successfully',
                'redirect' => $redirectRoute
            ]);
        } catch (\Exception $e) {
            Log::error('Translation error: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred during translation'], 500);
        }
    }
}
