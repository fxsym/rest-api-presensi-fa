<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PresenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 50; $i++) {
            DB::table('presences')->insert([
                'image' => fake()->imageUrl(640, 480, 'people'),
                'lab' => fake()->randomElement(['lab 1', 'lab 2', 'lab 3', 'lab 4', 'lab 5', 'lab 6',]),
                'status' => fake()->randomElement(['pending', 'validated']),
                'note' => fake()->optional()->sentence(),
                'user_id' => fake()->numberBetween(1, 15),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
