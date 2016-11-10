<?php

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandTableSeeder extends Seeder
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
                'name' => 'Adidas'
            ],
            [
                'name' => 'Nike'
            ],
            [
                'name' => 'Puma'
            ]
        ];

        foreach ($data as $value)
        {
            Brand::create($value);
        }
    }
}
