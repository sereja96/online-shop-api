<?php

use Illuminate\Database\Seeder;

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
                'role_id' => \App\Role::where('name', 'admin')->first()->id,
                'password' => \Illuminate\Support\Facades\Hash::make('qwerty')
            ],
            [
                'first_name' => 'test',
                'last_name' => 'user 1',
                'login' => 'test1',
                'email' => 'test@test.ru',
                'role_id' => \App\Role::where('name', 'user')->first()->id,
                'password' => \Illuminate\Support\Facades\Hash::make('qwerty')
            ],
            [
                'first_name' => 'test',
                'last_name' => 'user 2',
                'login' => 'test2',
                'email' => 'test2@test.ru',
                'role_id' => \App\Role::where('name', 'user')->first()->id,
                'password' => \Illuminate\Support\Facades\Hash::make('qwerty')
            ]
        ];

        foreach ($data as $item)
        {
            $user = new \App\User($item);
            $user->save();
        }
    }
}
