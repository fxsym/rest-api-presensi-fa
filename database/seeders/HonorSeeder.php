<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class HonorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('honors')->insert([
            [
            'category' => 'E',
            'amount' => 24500,
            'info' => 'Asisten yang akan mengasisteni 4X periode berturut-turut sebagai pengurus Forum Asisten',
            ],
            [
            'category' => 'F',
            'amount' => 20500,
            'info' => 'Asisten yang akan mengasisteni 4X periode berturut-turut bukan sebagai pengurus Forum Asisten',
            ],
            [
            'category' => 'G',
            'amount' => 22500,
            'info' => 'Asisten yang akan mengasisteni 3X periode berturut-turut sebagai pengurus Forum Asisten',
            ],
            [
            'category' => 'H',
            'amount' => 17000,
            'info' => 'Asisten yang akan mengasisteni 3X periode berturut-turut bukan sebagai pengurus Forum Asisten',
            ],
            [
            'category' => 'I',
            'amount' => 17500,
            'info' => 'Asisten yang akan mengasisteni 2X periode berturut-turut sebagai pengurus Forum Asisten',
            ],
            [
            'category' => 'J',
            'amount' => 14500,
            'info' => 'Asisten yang akan mengasisteni 2X periode berturut-turut bukan sebagai pengurus Forum Asisten',
            ],
            [
            'category' => 'K',
            'amount' => 12500,
            'info' => 'Asisten yang akan mengasisteni 1X',
            ],
        ]);
    }
}
