<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            [
                'name_lt' => 'Lietuva',
                'description_lt' => 'Lietuva yra valstybė Baltijos regione, esanti Baltijos jūros pakrantėje.',
            ],
            
        ];

        foreach ($countries as $country) {
            Country::create($country);
        }
    }
}
