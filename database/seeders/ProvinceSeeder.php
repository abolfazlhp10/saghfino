<?php

namespace Database\Seeders;

use App\Models\Province;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;


class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $result = Http::get('https://iranplacesapi.liara.run/api/provinces');        

        $provinces = $result->json();

    
        foreach ($provinces as $province) {
    
            Province::create([
                'name' => $province['name'],
            ]);
    
        }
    
        dd('success');
    }
}
