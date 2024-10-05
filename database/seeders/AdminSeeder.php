<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            ['name' => 'Admin','email' => 'admin@example.com','password' => bcrypt('password')],
        ]);

        DB::table('user_roles')->insert([
            ['user_id' => 1,'role_id' => 1]
        ]);

        DB::table('wallets')->insert([
            ['walletable_id' => 1,'walletable_type' => 'App\Models\User','balance' => 999999999999],
        ]);


    }
}
