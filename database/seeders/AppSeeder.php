<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Name;
use App\Models\Product;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AppSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 2; $i++)
        {
            if($i==0)
            {
                $name = 'Adnan Alkhouli';
                $email = 'mohamadalkhouliadnan@gmail.com';
            }
            else
            {
                $name = 'Mohammad AlHaifawi';
                $email = 'mowi@gmail.com';
            }

            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => bcrypt('123123'),
            ]);

            $user->wallet()->create(['balance' => 10000000000]);

            $isOwner = $i==0;

            if($isOwner)
            {
                $user->roles()->attach(['role_id' => 2]);

                for ($j = 0; $j < 20; $j++)
                {
                    $venue = $user->venues()->create([
                        'name' => fake()->company(),
                        'description' => fake()->paragraph(),
                        'longitude' => fake()->longitude(),
                        'latitude' => fake()->latitude(),
                        'available' => 1
                    ]);

                    Wallet::create(['walletable_id' => $venue->id,'walletable_type' => 'App\Models\Venue','balance' => 10000000000]);

                    for($d=1;$d<8;$d++)
                    {
                        $venue->times()->attach(['day_id' => $d],['start_time' => '08:00','end_time' => '16:00']);
                        $venue->times()->attach(['day_id' => $d],['start_time' => '18:00','end_time' => '21:00']);
                    }

                    for ($k = 0; $k < random_int(1,3); $k++)
                    {
                        $venue->phones()->create([
                            'phone_number' => fake()->unique()->phoneNumber(),
                        ]);
                    }

                    for($k = 0; $k < random_int(1,4); $k++)
                    {
                        $section = $venue->sections()->create([
                            'description' => fake()->paragraph(),
                            'name_id' => $k+1,
                            'capacity' => random_int(10,10000),
                            'price' => rand(10,10000000),
                            'available' => 1
                        ]);

                        $categories = Category::all()->random(random_int(1,5));

                        foreach($categories as $category)
                        {
                            $pivot = $section->categories_pivot()->create(['category_id' => $category->id]);

                            for($q=0;$q<random_int(1,3);$q++)
                            {
                                $pivot->levels()->create([
                                    'level' => fake()->text(20),
                                    'price' => rand(10,10000000)
                                ]);
                            }
                        }
                    }
                }

                for ($j = 0; $j < 20; $j++)
                {
                    $store = $user->stores()->create([
                        'name' => fake()->company(),
                        'description' => fake()->paragraph(),
                        'longitude' => fake()->longitude(),
                        'latitude' => fake()->latitude(),
                        'hasDelivery' => 1,
                        'deliveryCost' => rand(5,100),
                        'available' => 1
                    ]);

                    Wallet::create(['walletable_id' => $store->id,'walletable_type' => 'App\Models\Store','balance' => 10000000000]);

                    for($d=1;$d<8;$d++)
                    {
                        $store->times()->attach(['day_id' => $d],['start_time' => '08:00','end_time' => '16:00']);
                        $store->times()->attach(['day_id' => $d],['start_time' => '18:00','end_time' => '21:00']);
                    }

                    for ($k = 0; $k < random_int(1,3); $k++)
                    {
                        $store->phones()->create([
                            'phone_number' => fake()->unique()->phoneNumber(),
                        ]);
                    }

                    $products = Product::all()->random(random_int(10,30));

                    foreach($products as $product)
                    {
                        $store->products()->create([
                            'product_id' => $product->id,
                            'price' => rand(1,10000),
                            'available' => 1
                        ]);
                    }
                }


            }
        }
    }
}
