<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        $this->call(RolesSeeder::class);
        $this->call(AdminSeeder::class);
        $this->call(DaysSeeder::class);
        $this->call(CategoriesSeeder::class);
        $this->call(PromotionSeeder::class);
        $this->call(NameSeeder::class);
        // $this->call(ProductSeeder::class);
        // $this->call(AppSeeder::class);


        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
