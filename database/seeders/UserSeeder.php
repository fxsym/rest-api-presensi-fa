<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 15; $i++) {
            DB::table('users')->insert([
                'name' => fake()->name(),
                'nim' => fake()->unique()->numerify('##########'), // 10 digit angka
                'class' => 'TI-22-' . fake()->randomElement(['A', 'B', 'C', 'D']),
                'phone' => fake()->unique()->numerify('08##########'), // nomor hp indonesia
                'username' => fake()->unique()->userName(),
                'email' => fake()->unique()->safeEmail(),
                'email_verified_at' => now(),
                'password' => Hash::make('password'), // password default
                'presence' => fake()->numberBetween(0, 100), // misal presentase kehadiran
                'role' => fake()->randomElement(['admin', 'member']),
                'status' => fake()->randomElement(['active', 'inactive']),
                'image' => fake()->imageUrl(640, 480, 'people', true), // url gambar random
                'honors_id' => fake()->numberBetween(1, 4), // random id dari 1-4
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
