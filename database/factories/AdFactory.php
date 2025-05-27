<?php

namespace Database\Factories;

use App\Models\Ad;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdFactory extends Factory
{
    protected $model = Ad::class;

    public function definition(): array
    {
        $transactionType = $this->faker->randomElement(['sell', 'rent']);
        $propertyType = $this->faker->randomElement(['residential', 'commercial']);
        
        // Generate appropriate price based on transaction type
        $sellPrice = $transactionType === 'sell' ? $this->faker->numberBetween(1000000000, 5000000000) : null;
        $rentPrice = $transactionType === 'rent' ? $this->faker->numberBetween(5000000, 20000000) : null;
        $mortgagePrice = $transactionType === 'rent' ? $this->faker->numberBetween(100000000, 500000000) : null;

        return [
            'user_id' => 1,
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(3),
            'address' => $this->faker->address(),
            'area' => $this->faker->numberBetween(50, 300),
            'floor' => $this->faker->numberBetween(0, 20),
            'number_of_floors' => $this->faker->numberBetween(1, 30),
            'rooms' => $this->faker->numberBetween(1, 5),
            'city' => $this->faker->randomElement(['تهران', 'مشهد', 'اصفهان', 'شیراز', 'تبریز']),
            'type_of_wc' => $this->faker->randomElement(['farangi', 'wc', 'both']),
            'elevator' => $this->faker->boolean(70), // 70% chance of having elevator
            'parking' => $this->faker->boolean(80), // 80% chance of having parking
            'transaction_type' => $transactionType,
            'property_type' => $propertyType,
            'sell_price' => $sellPrice,
            'rent_price' => $rentPrice,
            'mortgage_price' => $mortgagePrice,
        ];
    }
} 