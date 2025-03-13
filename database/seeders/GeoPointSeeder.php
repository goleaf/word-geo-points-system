<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\GeoPoint;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GeoPointSeeder extends Seeder
{
    public function run(): void
    {
        $lithuaniaCities = City::where('country_id', 1)->pluck('id')->toArray();

        $loremIpsumPoints = [
            ['city_id' => $lithuaniaCities[array_rand($lithuaniaCities)], 'name_lt' => 'Lorem Ipsum 1', 'description_lt' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 'lat' => 54.6872, 'long' => 25.2797],
            ['city_id' => $lithuaniaCities[array_rand($lithuaniaCities)], 'name_lt' => 'Lorem Ipsum 2', 'description_lt' => 'Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'lat' => 54.6892, 'long' => 25.2795],
            ['city_id' => $lithuaniaCities[array_rand($lithuaniaCities)], 'name_lt' => 'Lorem Ipsum 3', 'description_lt' => 'Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris.', 'lat' => 54.6882, 'long' => 25.2790],
            ['city_id' => $lithuaniaCities[array_rand($lithuaniaCities)], 'name_lt' => 'Lorem Ipsum 4', 'description_lt' => 'Duis aute irure dolor in reprehenderit in voluptate velit esse.', 'lat' => 54.6862, 'long' => 25.2780],
            ['city_id' => $lithuaniaCities[array_rand($lithuaniaCities)], 'name_lt' => 'Lorem Ipsum 5', 'description_lt' => 'Excepteur sint occaecat cupidatat non proident, sunt in culpa.', 'lat' => 54.6852, 'long' => 25.2770],
            ['city_id' => $lithuaniaCities[array_rand($lithuaniaCities)], 'name_lt' => 'Lorem Ipsum 6', 'description_lt' => 'Sed ut perspiciatis unde omnis iste natus error sit voluptatem.', 'lat' => 54.6842, 'long' => 25.2760],
            ['city_id' => $lithuaniaCities[array_rand($lithuaniaCities)], 'name_lt' => 'Lorem Ipsum 7', 'description_lt' => 'Nemo enim ipsam voluptatem quia voluptas sit aspernatur.', 'lat' => 54.6832, 'long' => 25.2750],
            ['city_id' => $lithuaniaCities[array_rand($lithuaniaCities)], 'name_lt' => 'Lorem Ipsum 8', 'description_lt' => 'At vero eos et accusamus et iusto odio dignissimos ducimus.', 'lat' => 54.6822, 'long' => 25.2740],
            ['city_id' => $lithuaniaCities[array_rand($lithuaniaCities)], 'name_lt' => 'Lorem Ipsum 9', 'description_lt' => 'Temporibus autem quibusdam et aut officiis debitis aut rerum.', 'lat' => 54.6812, 'long' => 25.2730],
            ['city_id' => $lithuaniaCities[array_rand($lithuaniaCities)], 'name_lt' => 'Lorem Ipsum 10', 'description_lt' => 'Itaque earum rerum hic tenetur a sapiente delectus.', 'lat' => 54.6802, 'long' => 25.2720],
        ];

        foreach ($loremIpsumPoints as $geoPoint) {
            GeoPoint::create($geoPoint);
        }
    }
}