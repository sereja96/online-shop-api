<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
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
                'first_name' => 'Siarhei',
                'last_name' => 'Shablinski',
                'login' => 'sereja',
                'email' => 'webberry@bk.ru',
                'role_id' => Role::where('name', Role::ADMIN)->first()->id,
                'password' => Hash::make('qwerty')
            ],
            [
                'first_name' => 'test',
                'last_name' => 'user 1',
                'login' => 'test1',
                'email' => 'test@test.ru',
                'role_id' => Role::where('name', Role::USER)->first()->id,
                'password' => Hash::make('qwerty')
            ],
            [
                'first_name' => 'test',
                'last_name' => 'user 2',
                'login' => 'test2',
                'email' => 'test2@test.ru',
                'role_id' => Role::where('name', Role::SELLER)->first()->id,
                'password' => Hash::make('qwerty')
            ]
        ];

        foreach ($data as $item)
        {
            User::create($item);
        }
    }
}
