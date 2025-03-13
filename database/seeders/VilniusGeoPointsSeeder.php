<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use App\Models\GeoPoint;
use Illuminate\Database\Seeder;

class VilniusGeoPointsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Lithuania if it doesn't exist
        $lithuania = Country::firstOrCreate(
            ['name_lt' => 'Lietuva'],
            [
                'name_en' => 'Lithuania',
                'description_en' => 'Lithuania is a country in the Baltic region of Europe. It is one of the Baltic states and lies on the eastern shore of the Baltic Sea.',
                'description_lt' => 'Lietuva yra šalis Baltijos regione, Europoje. Tai viena iš Baltijos valstybių, esanti rytiniame Baltijos jūros krante.',
            ]
        );

        // Create Vilnius if it doesn't exist
        $vilnius = City::firstOrCreate(
            ['city_name_lt' => 'Vilnius', 'country_id' => $lithuania->id],
            [
                'city_name_en' => 'Vilnius',
                'city_description_en' => 'Vilnius is the capital and largest city of Lithuania. It is known for its baroque architecture, seen especially in its medieval Old Town.',
                'city_description_lt' => 'Vilnius yra Lietuvos sostinė ir didžiausias miestas. Jis žinomas dėl savo baroko architektūros, ypač matomos viduramžių senamiestyje.',
            ]
        );

        // Geo points data
        $geoPoints = [
            [
                'name_en' => 'Gediminas Tower',
                'name_lt' => 'Gedimino pilies bokštas',
                'description_en' => 'Gediminas Tower is the remaining part of the Upper Castle in Vilnius. The first wooden fortifications were built by Duke of the Grand Duchy of Lithuania, Gediminas.',
                'description_lt' => 'Gedimino pilies bokštas yra išlikusi Vilniaus Aukštutinės pilies dalis. Pirmuosius medinius įtvirtinimus pastatė Lietuvos Didžiosios Kunigaikštystės kunigaikštis Gediminas.',
                'lat' => 54.6869,
                'long' => 25.2900,
            ],
            [
                'name_en' => 'Vilnius Cathedral',
                'name_lt' => 'Vilniaus katedra',
                'description_en' => 'Vilnius Cathedral is the main Roman Catholic Cathedral of Lithuania. It is situated in Cathedral Square in the center of Vilnius Old Town.',
                'description_lt' => 'Vilniaus katedra yra pagrindinė Lietuvos Romos katalikų katedra. Ji yra Katedros aikštėje, Vilniaus senamiesčio centre.',
                'lat' => 54.6856,
                'long' => 25.2877,
            ],
            [
                'name_en' => 'Užupis',
                'name_lt' => 'Užupis',
                'description_en' => 'Užupis is a neighborhood in Vilnius, Lithuania, known for its artistic community and self-declared independence as the Republic of Užupis.',
                'description_lt' => 'Užupis yra Vilniaus rajonas, žinomas dėl savo menininkų bendruomenės ir paskelbtos nepriklausomybės kaip Užupio Respublika.',
                'lat' => 54.6814,
                'long' => 25.2938,
            ],
            [
                'name_en' => 'St. Anne\'s Church',
                'name_lt' => 'Šv. Onos bažnyčia',
                'description_en' => 'St. Anne\'s Church is a Roman Catholic church in Vilnius\' Old Town. It is a prominent example of both Flamboyant Gothic and Brick Gothic styles.',
                'description_lt' => 'Šv. Onos bažnyčia yra Romos katalikų bažnyčia Vilniaus senamiestyje. Tai ryškus liepsnojančios gotikos ir plytų gotikos stilių pavyzdys.',
                'lat' => 54.6828,
                'long' => 25.2924,
            ],
            [
                'name_en' => 'Vilnius Old Town',
                'name_lt' => 'Vilniaus senamiestis',
                'description_en' => 'Vilnius Old Town is one of the largest surviving medieval old towns in Northern Europe. It is a UNESCO World Heritage site.',
                'description_lt' => 'Vilniaus senamiestis yra vienas didžiausių išlikusių viduramžių senamiesčių Šiaurės Europoje. Tai UNESCO pasaulio paveldo objektas.',
                'lat' => 54.6795,
                'long' => 25.2893,
            ],
            [
                'name_en' => 'Cathedral Square',
                'name_lt' => 'Katedros aikštė',
                'description_en' => 'Cathedral Square is the main square of Vilnius Old Town, right in front of the Vilnius Cathedral. It is a key location for public events.',
                'description_lt' => 'Katedros aikštė yra pagrindinė Vilniaus senamiesčio aikštė, esanti priešais Vilniaus katedrą. Tai pagrindinė vieta viešiems renginiams.',
                'lat' => 54.6858,
                'long' => 25.2865,
            ],
            [
                'name_en' => 'Three Crosses Hill',
                'name_lt' => 'Trijų Kryžių kalnas',
                'description_en' => 'Three Crosses Hill is a prominent monument in Vilnius, consisting of three white crosses. It is located on a hill in Kalnai Park.',
                'description_lt' => 'Trijų Kryžių kalnas yra žymus paminklas Vilniuje, sudarytas iš trijų baltų kryžių. Jis yra ant kalvos Kalnų parke.',
                'lat' => 54.6875,
                'long' => 25.2968,
            ],
            [
                'name_en' => 'Gate of Dawn',
                'name_lt' => 'Aušros Vartai',
                'description_en' => 'The Gate of Dawn is a city gate in Vilnius, which was built between 1503 and 1522 as a part of defensive fortifications.',
                'description_lt' => 'Aušros Vartai yra miesto vartai Vilniuje, pastatyti tarp 1503 ir 1522 metų kaip gynybinių įtvirtinimų dalis.',
                'lat' => 54.6741,
                'long' => 25.2908,
            ],
            [
                'name_en' => 'Vilnius University',
                'name_lt' => 'Vilniaus universitetas',
                'description_en' => 'Vilnius University is one of the oldest universities in Northern Europe and the largest university in Lithuania.',
                'description_lt' => 'Vilniaus universitetas yra vienas seniausių universitetų Šiaurės Europoje ir didžiausias universitetas Lietuvoje.',
                'lat' => 54.6833,
                'long' => 25.2897,
            ],
            [
                'name_en' => 'Lukiškės Square',
                'name_lt' => 'Lukiškių aikštė',
                'description_en' => 'Lukiškės Square is the largest square in Vilnius, located in the center of the city. It has been recently renovated.',
                'description_lt' => 'Lukiškių aikštė yra didžiausia aikštė Vilniuje, esanti miesto centre. Ji neseniai buvo renovuota.',
                'lat' => 54.6908,
                'long' => 25.2705,
            ],
        ];

        // Create geo points
        foreach ($geoPoints as $pointData) {
            GeoPoint::firstOrCreate(
                [
                    'name_lt' => $pointData['name_lt'],
                    'city_id' => $vilnius->id
                ],
                [
                    'name_en' => $pointData['name_en'],
                    'description_en' => $pointData['description_en'],
                    'description_lt' => $pointData['description_lt'],
                    'lat' => $pointData['lat'],
                    'long' => $pointData['long'],
                ]
            );
        }

        $this->command->info('Vilnius geo points seeded successfully!');
    }
}
