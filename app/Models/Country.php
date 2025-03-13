<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\App;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [];

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

    protected $appends = ['name', 'description'];

    public function getNameAttribute()
    {
        $locale = App::getLocale();
        $field = "name_{$locale}";
        
        // Return the requested language field if it exists and is not empty
        if (isset($this->attributes[$field]) && !empty($this->attributes[$field])) {
            return $this->attributes[$field];
        }
        
        // Fallback to Lithuanian if the requested language field is empty or doesn't exist
        return $this->attributes['name_lt'] ?? '';
    }

    public function getDescriptionAttribute()
    {
        $locale = App::getLocale();
        $field = "description_{$locale}";
        
        // Return the requested language field if it exists and is not empty
        if (isset($this->attributes[$field]) && !empty($this->attributes[$field])) {
            return $this->attributes[$field];
        }
        
        // Fallback to Lithuanian if the requested language field is empty or doesn't exist
        return $this->attributes['description_lt'] ?? '';
    }

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }
}
