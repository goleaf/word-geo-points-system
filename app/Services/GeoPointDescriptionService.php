<?php

namespace App\Services;

use GrokPHP\Client\Config\ChatOptions;
use GrokPHP\Client\Enums\Model;
use GrokPHP\Laravel\Facades\GrokAI;
use Illuminate\Support\Facades\Log;

class GeoPointDescriptionService
{
    /**
     * Get language-specific requirements for geo point descriptions
     *
     * @param string $locale Language code
     * @return array Array with language requirements
     */
    public function getLanguageRequirements(string $locale): array
    {
        $languages = config('languages.languages');
        $defaultLocale = config('languages.default');

        // Default requirements
        $requirements = [
            'min_words' => 20,
            'max_words' => 50,
            'tone' => 'informative',
            'focus' => 'historical and cultural significance',
            'language_name' => $languages[$locale]['name'] ?? $locale
        ];

        // Check if we have specific requirements for this locale
        if (isset($languages[$locale]['requirements'])) {
            return array_merge($requirements, $languages[$locale]['requirements']);
        }

        return $requirements;
    }

    /**
     * Count words in a text
     *
     * @param string $text Text to count words in
     * @return int Number of words
     */
    public function countWords(string $text): int
    {
        // Remove extra whitespace and split by whitespace
        $words = preg_split('/\s+/', trim($text));

        // Filter out empty strings
        $words = array_filter($words, function($word) {
            return !empty($word);
        });

        return count($words);
    }

    /**
     * Generate a description for a geo point
     *
     * @param string $locationName Name of the location
     * @param string $locale Language code for the description
     * @param string|null $cityName Optional city name for context
     * @param string|null $countryName Optional country name for context
     * @return string|null Description or null on error
     */
    public function generateDescription(string $locationName, string $locale, ?string $cityName = null, ?string $countryName = null): ?string
    {
        try {
            // Get language requirements
            $requirements = $this->getLanguageRequirements($locale);

            // Create prompt for description generation
            $prompt = "Please write a brief, informative description in {$requirements['language_name']} for the following location:\n\n";
            $prompt .= "Location: {$locationName}";

            if ($cityName) {
                $prompt .= "\nCity: {$cityName}";
            }

            if ($countryName) {
                $prompt .= "\nCountry: {$countryName}";
            }

            $prompt .= "\n\nRequirements:";
            $prompt .= "\n- The description should be between {$requirements['min_words']} and {$requirements['max_words']} words";
            $prompt .= "\n- Use a {$requirements['tone']} tone";
            $prompt .= "\n- Focus on {$requirements['focus']}";
            $prompt .= "\n- Write in {$requirements['language_name']} language";
            $prompt .= "\n\nPlease respond only with the description text, without any additional comments or formatting.";

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

            // Validate word count
            $wordCount = $this->countWords($description);
            if ($wordCount < $requirements['min_words'] || $wordCount > $requirements['max_words']) {
                Log::warning("Description word count ({$wordCount}) outside of required range ({$requirements['min_words']}-{$requirements['max_words']})");
            }

            return $description;
        } catch (\Exception $e) {
            Log::error('Description generation error: ' . $e->getMessage());
            return null;
        }
    }
}
