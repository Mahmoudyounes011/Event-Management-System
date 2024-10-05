<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DaysSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('days')->insert([
            ['day' => 0],
            ['day' => 1],
            ['day' => 2],
            ['day' => 3],
            ['day' => 4],
            ['day' => 5],
            ['day' => 6],
        ]);
    }
}
