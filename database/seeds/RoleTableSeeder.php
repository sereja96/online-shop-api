<?php

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
                'name' => 'admin'
            ],
            [
                'name' => 'user'
            ]
        ];

        foreach ($data as $value)
        {
            $role = new \App\Role($value);
            $role->save();
        }
    }
}
