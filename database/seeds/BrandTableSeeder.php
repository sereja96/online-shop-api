<?php

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
            \App\Brand::create($value);
        }
    }
}
