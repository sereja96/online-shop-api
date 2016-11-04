<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RoleTableSeeder::class);
        $this->call(BrandTableSeeder::class);
        $this->call(CategoryTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(FollowerTableSeeder::class);
        $this->call(ShopTableSeeder::class);
        $this->call(ProductTableSeeder::class);
    }
}
