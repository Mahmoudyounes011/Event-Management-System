<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->insert([
            ['name' => 'Weddings'],
            ['name' => 'Parties'],
            ['name' => 'Scientific Meetings'],
            ['name' => 'Conferences'],
            ['name' => 'Consolation'],
            ['name' => 'Business Meetings'],
            ['name' => 'Religious Meetings'],
        ]);
    }
}
