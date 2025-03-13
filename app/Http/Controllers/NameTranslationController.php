<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Country;
use App\Models\GeoPoint;
use App\Services\TranslationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NameTranslationController extends Controller
{
    protected $translationService;

    public function __construct(TranslationService $translationService)
    {
        $this->translationService = $translationService;
    }

    /**
     * Translate entity name to the target language
     */
    public function translateName(Request $request)
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
            // Get the entity and its name based on entity type
            switch ($entityType) {
                case 'country':
                    $entity = Country::findOrFail($entityId);
                    $sourceText = $entity->{"name_{$sourceLocale}"};
                    $nameField = "name_{$targetLocale}";
                    break;
                case 'city':
                    $entity = City::findOrFail($entityId);
                    $sourceText = $entity->{"city_name_{$sourceLocale}"};
                    $nameField = "city_name_{$targetLocale}";
                    break;
                case 'geo_point':
                    $entity = GeoPoint::findOrFail($entityId);
                    $sourceText = $entity->{"name_{$sourceLocale}"};
                    $nameField = "name_{$targetLocale}";
                    break;
                default:
                    return response()->json(['error' => 'Invalid entity type'], 400);
            }

            // Check if source text exists
            if (empty($sourceText)) {
                return response()->json([
                    'error' => "No name available in the source language ({$sourceLocale})"
                ], 400);
            }

            // Translate the text
            $translatedText = $this->translationService->translate(
                $sourceText,
                $sourceLocale,
                $targetLocale,
                $entityType,
                null,
                true // Indicate this is a name translation
            );

            if ($translatedText === null) {
                return response()->json(['error' => 'Translation failed'], 500);
            }

            // Update the entity with the translated text
            $entity->{$nameField} = $translatedText;
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
                'message' => 'Name translation completed successfully',
                'redirect' => $redirectRoute
            ]);
        } catch (\Exception $e) {
            Log::error('Name translation error: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred during name translation'], 500);
        }
    }

    /**
     * Translate all names for an entity to all target languages
     */
    public function translateAllNames(Request $request)
    {
        $request->validate([
            'entity_type' => 'required|string|in:country,city,geo_point',
            'entity_id' => 'required|integer',
            'source_locale' => 'required|string|size:2',
        ]);

        $entityType = $request->entity_type;
        $entityId = $request->entity_id;
        $sourceLocale = $request->source_locale;
        $currentLocale = app('currentLocale');
        $availableLocales = config('languages.available', []);

        try {
            // Get the entity and its name based on entity type
            switch ($entityType) {
                case 'country':
                    $entity = Country::findOrFail($entityId);
                    $sourceText = $entity->{"name_{$sourceLocale}"};
                    $nameFieldPrefix = "name_";
                    break;
                case 'city':
                    $entity = City::findOrFail($entityId);
                    $sourceText = $entity->{"city_name_{$sourceLocale}"};
                    $nameFieldPrefix = "city_name_";
                    break;
                case 'geo_point':
                    $entity = GeoPoint::findOrFail($entityId);
                    $sourceText = $entity->{"name_{$sourceLocale}"};
                    $nameFieldPrefix = "name_";
                    break;
                default:
                    return response()->json(['error' => 'Invalid entity type'], 400);
            }

            // Check if source text exists
            if (empty($sourceText)) {
                return response()->json([
                    'error' => "No name available in the source language ({$sourceLocale})"
                ], 400);
            }

            $translatedCount = 0;

            // Translate to all available locales except the source
            foreach ($availableLocales as $targetLocale) {
                if ($targetLocale === $sourceLocale) {
                    continue;
                }

                $nameField = $nameFieldPrefix . $targetLocale;

                // Skip if the target field already has content
                if (!empty($entity->{$nameField})) {
                    continue;
                }

                // Translate the text
                $translatedText = $this->translationService->translate(
                    $sourceText,
                    $sourceLocale,
                    $targetLocale,
                    $entityType,
                    null,
                    true // Indicate this is a name translation
                );

                if ($translatedText !== null) {
                    $entity->{$nameField} = $translatedText;
                    $translatedCount++;
                }
            }

            // Save the entity with all translations
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
                'message' => "Successfully translated name to {$translatedCount} languages",
                'redirect' => $redirectRoute
            ]);
        } catch (\Exception $e) {
            Log::error('Name translation error: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred during name translation'], 500);
        }
    }
}
