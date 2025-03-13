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
];