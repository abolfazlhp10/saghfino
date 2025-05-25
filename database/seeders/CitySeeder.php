<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use App\Models\City;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $result = Http::get('https://iranplacesapi.liara.run/api/cities');

        $cities = $result->json();


        foreach ($cities as $city) {

            City::create([
                'name' => $city['name'],
                'province_id' => $city['province_id']
            ]);
        }

        dd('success');
    }
}
