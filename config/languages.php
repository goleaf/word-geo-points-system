<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Available Application Languages
    |--------------------------------------------------------------------------
    |
    | This array contains the available languages for your application.
    | Each language has a code, name, native name, and validation rules.
    |
    */

    'languages' => [
        'lt' => [
            'name' => 'Lithuanian',
            'native' => 'Lietuvių',
            'validation' => [
                'country_description_min' => 10,
                'country_description_max' => 500,
                'city_description_min' => 5,
                'city_description_max' => 300,
                'geo_point_description_min' => 3,
                'geo_point_description_max' => 200,
            ],
            'requirements' => [
                'min_words' => 15,
                'max_words' => 40,
                'tone' => 'informative',
                'focus' => 'historical and cultural significance'
            ],
        ],
        'ru' => [
            'name' => 'Russian',
            'native' => 'Русский',
            'validation' => [
                'country_description_min' => 10,
                'country_description_max' => 500,
                'city_description_min' => 5,
                'city_description_max' => 300,
                'geo_point_description_min' => 3,
                'geo_point_description_max' => 200,
            ],
            'requirements' => [
                'min_words' => 20,
                'max_words' => 50,
                'tone' => 'formal',
                'focus' => 'historical significance and architectural details'
            ],
        ],
        'pl' => [
            'name' => 'Polish',
            'native' => 'Polski',
            'validation' => [
                'country_description_min' => 10,
                'country_description_max' => 500,
                'city_description_min' => 5,
                'city_description_max' => 300,
                'geo_point_description_min' => 3,
                'geo_point_description_max' => 200,
            ],
            'requirements' => [
                'min_words' => 20,
                'max_words' => 45,
                'tone' => 'informative',
                'focus' => 'cultural and historical context'
            ],
        ],
        'de' => [
            'name' => 'German',
            'native' => 'Deutsch',
            'validation' => [
                'country_description_min' => 10,
                'country_description_max' => 500,
                'city_description_min' => 5,
                'city_description_max' => 300,
                'geo_point_description_min' => 3,
                'geo_point_description_max' => 200,
            ],
            'requirements' => [
                'min_words' => 25,
                'max_words' => 60,
                'tone' => 'precise',
                'focus' => 'factual information and technical details'
            ],
        ],
        'fr' => [
            'name' => 'French',
            'native' => 'Français',
            'validation' => [
                'country_description_min' => 10,
                'country_description_max' => 500,
                'city_description_min' => 5,
                'city_description_max' => 300,
                'geo_point_description_min' => 3,
                'geo_point_description_max' => 200,
            ],
            'requirements' => [
                'min_words' => 25,
                'max_words' => 55,
                'tone' => 'elegant',
                'focus' => 'aesthetic and cultural aspects'
            ],
        ],
        'fi' => [
            'name' => 'Finnish',
            'native' => 'Suomi',
            'validation' => [
                'country_description_min' => 10,
                'country_description_max' => 500,
                'city_description_min' => 5,
                'city_description_max' => 300,
                'geo_point_description_min' => 3,
                'geo_point_description_max' => 200,
            ],
            'requirements' => [
                'min_words' => 15,
                'max_words' => 40,
                'tone' => 'straightforward',
                'focus' => 'practical information and natural features'
            ],
        ],
        'no' => [
            'name' => 'Norwegian',
            'native' => 'Norsk',
            'validation' => [
                'country_description_min' => 10,
                'country_description_max' => 500,
                'city_description_min' => 5,
                'city_description_max' => 300,
                'geo_point_description_min' => 3,
                'geo_point_description_max' => 200,
            ],
            'requirements' => [
                'min_words' => 15,
                'max_words' => 40,
                'tone' => 'clear',
                'focus' => 'natural beauty and outdoor activities'
            ],
        ],
        'sv' => [
            'name' => 'Swedish',
            'native' => 'Svenska',
            'validation' => [
                'country_description_min' => 10,
                'country_description_max' => 500,
                'city_description_min' => 5,
                'city_description_max' => 300,
                'geo_point_description_min' => 3,
                'geo_point_description_max' => 200,
            ],
            'requirements' => [
                'min_words' => 15,
                'max_words' => 45,
                'tone' => 'balanced',
                'focus' => 'cultural significance and modern context'
            ],
        ],
        'dk' => [
            'name' => 'Danish',
            'native' => 'Dansk',
            'validation' => [
                'country_description_min' => 10,
                'country_description_max' => 500,
                'city_description_min' => 5,
                'city_description_max' => 300,
                'geo_point_description_min' => 3,
                'geo_point_description_max' => 200,
            ],
            'requirements' => [
                'min_words' => 15,
                'max_words' => 40,
                'tone' => 'friendly',
                'focus' => 'design aspects and historical context'
            ],
        ],
        'en' => [
            'name' => 'English',
            'native' => 'English',
            'validation' => [
                'country_description_min' => 10,
                'country_description_max' => 500,
                'city_description_min' => 5,
                'city_description_max' => 300,
                'geo_point_description_min' => 3,
                'geo_point_description_max' => 200,
            ],
            'requirements' => [
                'min_words' => 25,
                'max_words' => 60,
                'tone' => 'informative',
                'focus' => 'comprehensive overview with historical and cultural context'
            ],
        ],
        'ch' => [
            'name' => 'Chinese',
            'native' => '中文',
            'validation' => [
                'country_description_min' => 10,
                'country_description_max' => 500,
                'city_description_min' => 5,
                'city_description_max' => 300,
                'geo_point_description_min' => 3,
                'geo_point_description_max' => 200,
            ],
            'requirements' => [
                'min_words' => 20,
                'max_words' => 50,
                'tone' => 'respectful',
                'focus' => 'cultural significance and historical importance'
            ],
        ],
        'kr' => [
            'name' => 'Korean',
            'native' => '한국어',
            'validation' => [
                'country_description_min' => 10,
                'country_description_max' => 500,
                'city_description_min' => 5,
                'city_description_max' => 300,
                'geo_point_description_min' => 3,
                'geo_point_description_max' => 200,
            ],
            'requirements' => [
                'min_words' => 20,
                'max_words' => 45,
                'tone' => 'polite',
                'focus' => 'cultural and historical significance'
            ],
        ],
        'lv' => [
            'name' => 'Latvian',
            'native' => 'Latviešu',
            'validation' => [
                'country_description_min' => 10,
                'country_description_max' => 500,
                'city_description_min' => 5,
                'city_description_max' => 300,
                'geo_point_description_min' => 3,
                'geo_point_description_max' => 200,
            ],
            'requirements' => [
                'min_words' => 15,
                'max_words' => 40,
                'tone' => 'informative',
                'focus' => 'local significance and historical context'
            ],
        ],
        'ee' => [
            'name' => 'Estonian',
            'native' => 'Eesti',
            'validation' => [
                'country_description_min' => 10,
                'country_description_max' => 500,
                'city_description_min' => 5,
                'city_description_max' => 300,
                'geo_point_description_min' => 3,
                'geo_point_description_max' => 200,
            ],
            'requirements' => [
                'min_words' => 15,
                'max_words' => 40,
                'tone' => 'clear',
                'focus' => 'historical context and modern relevance'
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Language
    |--------------------------------------------------------------------------
    |
    | This is the default language that will be used by the application.
    |
    */

    'default' => 'lt',

    /*
    |--------------------------------------------------------------------------
    | Available Locales
    |--------------------------------------------------------------------------
    |
    | This is a list of all available locales for easy access.
    |
    */

    'available' => ['lt', 'ru', 'pl', 'de', 'fr', 'fi', 'no', 'sv', 'dk', 'en', 'ch', 'kr', 'lv', 'ee'],
];
