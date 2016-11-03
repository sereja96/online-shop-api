<?php

use Illuminate\Database\Seeder;

class ProductTableSeeder extends Seeder
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
                'shop_id' => \App\Shop::where('name', 'Test Shop')->first()->id || 1,
                'category_id' => \App\Category::where('name', 'Boots')->first()->id || 1,
                'brand_id' => \App\Brand::where('name', 'Adidas')->first()->id || 1,
                'name' => 'Adidas Boots',
                'description' => 'Cool Boots for real sport man',
                'price' => 120.5
            ],
            [
                'shop_id' => \App\Shop::where('name', 'Test Shop')->first()->id || 1,
                'category_id' => \App\Category::where('name', 'Shorts')->first()->id || 1,
                'brand_id' => \App\Brand::where('name', 'Puma')->first()->id || 1,
                'name' => 'Puma Shorts M-size',
                'price' => 70
            ]
        ];

        foreach ($data as $value)
        {
            \App\Product::create($value);
        }
    }
}
