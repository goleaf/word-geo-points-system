<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeoPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'city_id',
        'lat',
        'long',
    ];

    // Add dynamic fillable fields for all languages
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        
        $availableLocales = array_keys(config('languages.languages') ?? []);
        
        foreach ($availableLocales as $locale) {
            $this->fillable[] = "name_{$locale}";
            $this->fillable[] = "description_{$locale}";
        }
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
}
