<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id',
    ];

    // Add dynamic fillable fields for all languages
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        
        $availableLocales = array_keys(config('languages.languages') ?? []);
        
        foreach ($availableLocales as $locale) {
            $this->fillable[] = "city_name_{$locale}";
            $this->fillable[] = "city_description_{$locale}";
        }
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function geoPoints(): HasMany
    {
        return $this->hasMany(GeoPoint::class);
    }
}
