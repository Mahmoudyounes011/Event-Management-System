<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [];
        for($i=0;$i<100;$i++)
            $products[] = ['name' => fake()->text(10),'description' => fake()->paragraph()];

        DB::table('products')->insert($products);
    }
}
