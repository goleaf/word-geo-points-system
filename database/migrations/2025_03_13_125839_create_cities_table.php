<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained()->onDelete('cascade');
            
            // Add language-specific fields for each supported language
            $languages = config('languages.languages', []);
            foreach (array_keys($languages) as $locale) {
                $table->string("city_name_{$locale}")->nullable();
                $table->text("city_description_{$locale}")->nullable();
            }
            
            // Make the default language fields required
            $defaultLocale = config('languages.default', 'lt');
            $table->string("city_name_{$defaultLocale}")->nullable(false)->change();
            
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('long', 10, 7)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
