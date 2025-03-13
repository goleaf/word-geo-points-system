<?php

namespace App\Services;

use GrokPHP\Client\Config\ChatOptions;
use GrokPHP\Client\Enums\Model;
use GrokPHP\Laravel\Facades\GrokAI;
use Illuminate\Support\Facades\Log;

class TranslationService
{
    /**
     * Translate text from source language to target language using Grok-2
     *
     * @param string $text Text to translate
     * @param string $sourceLocale Source language code
     * @param string $targetLocale Target language code
     * @param string $entityType Type of entity (country, city, geo_point)
     * @param string|null $entityName Name of the entity (optional for name translations)
     * @param bool $isName Whether this is a name translation (default: false)
     * @return string|null Translated text or null on error
     */
    public function translate(string $text, string $sourceLocale, string $targetLocale, string $entityType, ?string $entityName = null, bool $isName = false): ?string
    {
        try {
            // Get language names from codes
            $sourceLanguage = config("languages.languages.{$sourceLocale}.name") ?? $sourceLocale;
            $targetLanguage = config("languages.languages.{$targetLocale}.name") ?? $targetLocale;

            // Create prompt for translation
            if ($isName) {
                // Prompt for name translation
                $prompt = "Translate the following {$entityType} name from {$sourceLanguage} to {$targetLanguage}. " .
                         "Preserve proper nouns as appropriate. " .
                         "Return only the translated name without any additional comments or explanations.\n\n" .
                         "Name to translate:\n{$text}";
            } else {
                // Prompt for description translation
                $prompt = "Translate the following {$entityType} description from {$sourceLanguage} to {$targetLanguage}. ";

                if ($entityName) {
                    $prompt .= "The {$entityType} name is '{$entityName}'. ";
                }

                $prompt .= "Maintain the same tone, style, and information content. " .
                         "Return only the translated text without any additional comments or explanations.\n\n" .
                         "Text to translate:\n{$text}";
            }

            // Call Grok-2 API with appropriate temperature
            $temperature = $isName ? 0.1 : 0.3; // Lower temperature for names to be more precise
            $response = GrokAI::chat(
                [['role' => 'user', 'content' => $prompt]],
                new ChatOptions(model: Model::GROK_2, temperature: $temperature)
            );

            // Get the translated text
            $translatedText = $response->content();

            // Clean up the response (remove any potential markdown formatting or quotes)
            $translatedText = trim($translatedText);
            $translatedText = preg_replace('/^["\']|["\']$/m', '', $translatedText);

            return $translatedText;
        } catch (\Exception $e) {
            Log::error('Translation error: ' . $e->getMessage());
            return null;
        }
    }
}
