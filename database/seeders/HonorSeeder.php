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
                'category' => 'A',
                'amount' => 24500,
                'info' => 'Asisten yang akan mengasisteni 6X periode berturut-turut bukan sebagai pengurus Forum Asisten tapi sudah lulus',
            ],
            [
                'category' => 'B',
                'amount' => 24500,
                'info' => 'Asisten yang akan mengasisteni 6X periode berturut-turut bukan sebagai pengurus Forum Asisten belum lulus',
            ],
            [
                'category' => 'C',
                'amount' => 26500,
                'info' => 'Asisten yang akan mengasisteni 5X periode berturut-turut sebagai pengurus Forum Asisten',
            ],
            [
                'category' => 'D',
                'amount' => 22500,
                'info' => 'Asisten yang akan mengasisteni 5X periode berturut-turut bukan sebagai pengurus Forum Asisten',
            ],

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
