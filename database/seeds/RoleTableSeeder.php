<?php

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder
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
                'name' => Role::ADMIN
            ],
            [
                'name' => Role::USER
            ],
            [
                'name' => Role::SELLER
            ]
        ];

        foreach ($data as $value)
        {
            Role::create($value);
        }
    }
}
