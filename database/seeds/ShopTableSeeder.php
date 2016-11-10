<?php

use App\Models\Shop;
use Illuminate\Database\Seeder;

class ShopTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'name' => 'Test Shop',
                'description' => 'super super shop.',
                'user_id' => 1
            ],
            [
                'name' => 'Super Shop',
                'user_id' => 1
            ],
            [
                'name' => 'Best Shop',
                'user_id' => 3
            ]
        ];

        foreach ($data as $value)
        {
            Shop::create($value);
        }
    }
}
