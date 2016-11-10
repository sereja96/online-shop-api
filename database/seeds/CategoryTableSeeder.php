<?php

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategoryTableSeeder extends Seeder
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
                'name' => 'Shorts'
            ],
            [
                'name' => 'T-Shirts'
            ],
            [
                'name' => 'Boots'
            ]
        ];

        foreach ($data as $value)
        {
            Category::create($value);
        }
    }
}
