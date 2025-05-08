<?php
namespace Database\Seeders;

use App\Models\Ad;
use Illuminate\Database\Seeder;

class AdSeeder extends Seeder
{
    public function run(): void
    {
        // Create 50 ads with random users
        Ad::factory(50)
            ->create();

    }
}
