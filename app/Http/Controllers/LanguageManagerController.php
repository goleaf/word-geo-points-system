<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\App;

class LanguageManagerController extends Controller
{
    /**
     * Display a listing of the languages.
     */
    public function index(Request $request)
    {
        $currentLocale = app('currentLocale');
        $availableLocales = array_keys(config('languages.languages') ?? []);

        $languages = config('languages.languages') ?? [];

        return view('language-manager.index', compact('languages', 'availableLocales'));
    }

    /**
     * Show the form for creating a new language.
     */
    public function create(Request $request)
    {
        $currentLocale = app('currentLocale');
        $availableLocales = array_keys(config('languages.languages') ?? []);

        return view('language-manager.form', compact('currentLocale', 'availableLocales'));
    }

    /**
     * Store a newly created language in storage.
     */
    public function store(Request $request)
    {
        $currentLocale = app('currentLocale');
        $availableLocales = array_keys(config('languages.languages') ?? []);

        $request->validate([
            'code' => 'required|string|size:2|unique:languages,code',
            'name' => 'required|string|max:255',
            'native_name' => 'required|string|max:255',
            'min_words' => 'required|integer|min:1',
            'max_words' => 'required|integer|min:1|gt:min_words',
        ]);

        $code = $request->code;
        $name = $request->name;
        $native = $request->native_name;

        // Check if language already exists
        $languages = config('languages.languages') ?? [];
        if (isset($languages[$code])) {
            return redirect()->route('language-manager.index', ['currentLocale' => $currentLocale])
                ->with('error', "Language with code '{$code}' already exists!");
        }

        // Add language to config file
        $configPath = config_path('languages.php');
        $configContent = file_get_contents($configPath);

        $newLanguage = "        '{$code}' => [\n";
        $newLanguage .= "            'name' => '{$name}',\n";
        $newLanguage .= "            'native' => '{$native}',\n";
        $newLanguage .= "            'code' => '{$code}',\n";
        $newLanguage .= "            'validation' => [\n";
        $newLanguage .= "                'country_description_min' => {$request->min_words},\n";
        $newLanguage .= "                'country_description_max' => {$request->max_words},\n";
        $newLanguage .= "            ],\n";
        $newLanguage .= "        ],\n";

        // Find the position to insert the new language
        $position = strpos($configContent, "'languages' => [") + strlen("'languages' => [") + 1;
        $configContent = substr_replace($configContent, $newLanguage, $position, 0);

        file_put_contents($configPath, $configContent);

        // Add language fields to database tables
        $this->addLanguageFieldsToTables($code);

        return redirect()->route('language-manager.index', ['currentLocale' => $currentLocale])
            ->with('success', "Language '{$name}' ({$code}) added successfully!");
    }

    /**
     * Show the form for editing the specified language.
     */
    public function edit(Request $request, $code)
    {
        $currentLocale = app('currentLocale');
        $availableLocales = array_keys(config('languages.languages') ?? []);

        $languages = config('languages.languages') ?? [];

        if (!isset($languages[$code])) {
            return redirect()->route('language-manager.index', ['currentLocale' => $currentLocale])
                ->with('error', "Language with code '{$code}' does not exist!");
        }

        $language = $languages[$code];
        $language['code'] = $code;

        return view('language-manager.form', compact('language', 'code', 'currentLocale', 'availableLocales'));
    }

    /**
     * Update the specified language in storage.
     */
    public function update(Request $request, $code)
    {
        $currentLocale = app('currentLocale');
        $availableLocales = array_keys(config('languages.languages') ?? []);

        $request->validate([
            'name' => 'required|string|max:50',
            'native' => 'required|string|max:50',
            'country_description_min' => 'required|integer|min:0',
            'country_description_max' => 'required|integer|min:0|gt:country_description_min',
            'city_description_min' => 'required|integer|min:0',
            'city_description_max' => 'required|integer|min:0|gt:city_description_min',
            'geo_point_description_min' => 'required|integer|min:0',
            'geo_point_description_max' => 'required|integer|min:0|gt:geo_point_description_min',
        ]);

        // Check if language exists
        $languages = config('languages.languages') ?? [];
        if (!isset($languages[$code])) {
            return redirect()->route('language-manager.index', ['currentLocale' => $currentLocale])
                ->with('error', "Language with code '{$code}' does not exist!");
        }

        // Update language in config file
        $configPath = config_path('languages.php');
        $configContent = file_get_contents($configPath);

        $pattern = "/        '{$code}' => \[\n.*?'code' => '{$code}',\n.*?\],\n/s";

        $updatedLanguage = "        '{$code}' => [\n";
        $updatedLanguage .= "            'name' => '{$request->name}',\n";
        $updatedLanguage .= "            'native' => '{$request->native}',\n";
        $updatedLanguage .= "            'code' => '{$code}',\n";

        // Preserve is_default if it exists
        if (isset($languages[$code]['is_default']) && $languages[$code]['is_default']) {
            $updatedLanguage .= "            'is_default' => true,\n";
        }

        $updatedLanguage .= "            'validation' => [\n";
        $updatedLanguage .= "                'country_description_min' => {$request->country_description_min},\n";
        $updatedLanguage .= "                'country_description_max' => {$request->country_description_max},\n";
        $updatedLanguage .= "                'city_description_min' => {$request->city_description_min},\n";
        $updatedLanguage .= "                'city_description_max' => {$request->city_description_max},\n";
        $updatedLanguage .= "                'geo_point_description_min' => {$request->geo_point_description_min},\n";
        $updatedLanguage .= "                'geo_point_description_max' => {$request->geo_point_description_max},\n";
        $updatedLanguage .= "            ],\n";
        $updatedLanguage .= "        ],\n";

        $configContent = preg_replace($pattern, $updatedLanguage, $configContent);

        file_put_contents($configPath, $configContent);

        return redirect()->route('language-manager.index', ['currentLocale' => $currentLocale])
            ->with('success', "Language '{$request->name}' ({$code}) updated successfully!");
    }

    /**
     * Remove the specified language from storage.
     */
    public function destroy(Request $request, $code)
    {
        $currentLocale = app('currentLocale');
        $availableLocales = array_keys(config('languages.languages') ?? []);

        // Check if language exists
        $languages = config('languages.languages') ?? [];
        if (!isset($languages[$code])) {
            return redirect()->route('language-manager.index', ['currentLocale' => $currentLocale])
                ->with('error', "Language with code '{$code}' does not exist!");
        }

        // Check if it's the default language
        if (isset($languages[$code]['is_default']) && $languages[$code]['is_default']) {
            return redirect()->route('language-manager.index', ['currentLocale' => $currentLocale])
                ->with('error', "Cannot remove the default language!");
        }

        // Remove language from config file
        $configPath = config_path('languages.php');
        $configContent = file_get_contents($configPath);

        $pattern = "/        '{$code}' => \[\n.*?'code' => '{$code}',\n.*?\],\n/s";
        $configContent = preg_replace($pattern, '', $configContent);

        file_put_contents($configPath, $configContent);

        // Remove language fields from database tables
        $this->removeLanguageFieldsFromTables($code);

        return redirect()->route('language-manager.index', ['currentLocale' => $currentLocale])
            ->with('success', "Language '{$languages[$code]['name']}' ({$code}) removed successfully!");
    }

    /**
     * Add language fields to database tables
     */
    private function addLanguageFieldsToTables($code)
    {
        $tables = [
            'countries' => [
                'name' => "name_{$code}",
                'description' => "description_{$code}",
            ],
            'cities' => [
                'name' => "city_name_{$code}",
                'description' => "city_description_{$code}",
            ],
            'geo_points' => [
                'name' => "name_{$code}",
                'description' => "description_{$code}",
            ],
        ];

        foreach ($tables as $table => $fields) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) use ($fields) {
                    foreach ($fields as $field) {
                        if (!Schema::hasColumn($table->getTable(), $field)) {
                            $table->string($field)->nullable();
                        }
                    }
                });
            }
        }
    }

    /**
     * Remove language fields from database tables
     */
    private function removeLanguageFieldsFromTables($code)
    {
        $tables = [
            'countries' => [
                'name' => "name_{$code}",
                'description' => "description_{$code}",
            ],
            'cities' => [
                'name' => "city_name_{$code}",
                'description' => "city_description_{$code}",
            ],
            'geo_points' => [
                'name' => "name_{$code}",
                'description' => "description_{$code}",
            ],
        ];

        foreach ($tables as $table => $fields) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) use ($fields) {
                    foreach ($fields as $field) {
                        if (Schema::hasColumn($table->getTable(), $field)) {
                            $table->dropColumn($field);
                        }
                    }
                });
            }
        }
    }
}
