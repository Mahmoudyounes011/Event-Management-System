<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $names = [];
        for($number=1;$number<=40;$number++)
            $names[] = ['name' => 'Section '.$number];

        DB::table('names')->insert($names);
    }
}
