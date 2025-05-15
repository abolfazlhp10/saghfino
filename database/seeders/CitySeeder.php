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
        $result = Http::get('https://www.iran-locations-api.ir/api/v1/fa/cities');

        $cities = $result->json();
    
        foreach ($cities as $city) {
    
            City::create([
                'name' => $city['name'],
            ]);
    
        }
    
        echo 'success';
    }
}
