<?php

namespace App\Services;

use GrokPHP\Client\Config\ChatOptions;
use GrokPHP\Client\Enums\Model;
use GrokPHP\Laravel\Facades\GrokAI;
use Illuminate\Support\Facades\Log;

class GeocodingService
{
    /**
     * Get coordinates for a location name using Grok-2
     *
     * @param string $locationName Name of the location to geocode
     * @param string|null $cityName Optional city name for context
     * @param string|null $countryName Optional country name for context
     * @return array|null Array with lat and lng keys or null on error
     */
    public function getCoordinates(string $locationName, ?string $cityName = null, ?string $countryName = null): ?array
    {
        try {
            // Create prompt for geocoding
            $prompt = "I need the exact latitude and longitude coordinates for the following location:\n\n";
            $prompt .= "Location: {$locationName}";

            if ($cityName) {
                $prompt .= "\nCity: {$cityName}";
            }

            if ($countryName) {
                $prompt .= "\nCountry: {$countryName}";
            }

            $prompt .= "\n\nPlease respond ONLY with the coordinates in this exact JSON format: {\"lat\": 12.3456789, \"long\": 67.8901234}";
            $prompt .= "\nEnsure that coordinates have exactly 7 decimal places for precision.";
            $prompt .= "\nDo not include any other text, explanations, or formatting.";

            // Call Grok-2 API
            $response = GrokAI::chat(
                [['role' => 'user', 'content' => $prompt]],
                new ChatOptions(model: Model::GROK_2, temperature: 0.1)
            );

            // Get the coordinates
            $responseText = $response->content();

            // Clean up the response and extract JSON
            $responseText = trim($responseText);

            // Extract JSON if it's wrapped in code blocks or other text
            if (preg_match('/\{.*\}/s', $responseText, $matches)) {
                $jsonStr = $matches[0];
                $coordinates = json_decode($jsonStr, true);

                if (isset($coordinates['lat']) && (isset($coordinates['long']) || isset($coordinates['lng']))) {
                    $lat = (float) $coordinates['lat'];
                    $long = (float) (isset($coordinates['long']) ? $coordinates['long'] : $coordinates['lng']);

                    // Format coordinates to have 7 decimal places
                    return [
                        'lat' => number_format($lat, 7, '.', ''),
                        'long' => number_format($long, 7, '.', '')
                    ];
                }
            }

            Log::warning('Invalid geocoding response format: ' . $responseText);
            return null;
        } catch (\Exception $e) {
            Log::error('Geocoding error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get description for a location using Grok-2
     *
     * @param string $locationName Name of the location
     * @param string $locale Language code for the description
     * @param string|null $cityName Optional city name for context
     * @param string|null $countryName Optional country name for context
     * @return string|null Description or null on error
     */
    public function getDescription(string $locationName, string $locale, ?string $cityName = null, ?string $countryName = null): ?string
    {
        try {
            // Get language name from code
            $language = config("languages.languages.{$locale}.name") ?? $locale;

            // Create prompt for description generation
            $prompt = "Please write a brief, informative description in {$language} for the following location:\n\n";
            $prompt .= "Location: {$locationName}";

            if ($cityName) {
                $prompt .= "\nCity: {$cityName}";
            }

            if ($countryName) {
                $prompt .= "\nCountry: {$countryName}";
            }

            $prompt .= "\n\nThe description should be 2-3 sentences long, factual, and highlight key features or historical significance of the location.";
            $prompt .= "\nPlease respond only with the description text, without any additional comments or formatting.";

            // Call Grok-2 API
            $response = GrokAI::chat(
                [['role' => 'user', 'content' => $prompt]],
                new ChatOptions(model: Model::GROK_2, temperature: 0.7)
            );

            // Get the description
            $description = $response->content();

            // Clean up the response
            $description = trim($description);
            $description = preg_replace('/^["\']|["\']$/m', '', $description);

            return $description;
        } catch (\Exception $e) {
            Log::error('Description generation error: ' . $e->getMessage());
            return null;
        }
    }
}
