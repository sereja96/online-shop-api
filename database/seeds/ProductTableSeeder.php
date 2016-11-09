<?php

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Shop;
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
                'shop_id' => Shop::where('name', 'Test Shop')->first()->id || 1,
                'category_id' => Category::where('name', 'Boots')->first()->id || 1,
                'brand_id' => Brand::where('name', 'Adidas')->first()->id || 1,
                'name' => 'Adidas Boots',
                'description' => 'Cool Boots for real sport man',
                'price' => 120.5
            ],
            [
                'shop_id' => Shop::where('name', 'Test Shop')->first()->id || 1,
                'category_id' => Category::where('name', 'Shorts')->first()->id || 1,
                'brand_id' => Brand::where('name', 'Puma')->first()->id || 1,
                'name' => 'Puma Shorts M-size',
                'price' => 70
            ]
        ];

        foreach ($data as $value)
        {
            Product::create($value);
        }
    }
}
