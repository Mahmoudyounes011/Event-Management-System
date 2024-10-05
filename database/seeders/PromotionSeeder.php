<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PromotionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('promotions')->insert([
            ['type' => 'Suggestion','description' => 'Suggestion desc','cost' => 5], // cost per hour
            ['type' => 'notification','description' => 'nitification desc','cost' => 20], 
        ]);
    }
}
