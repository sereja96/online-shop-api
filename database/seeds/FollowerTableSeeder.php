<?php

use App\Models\Follower;
use Illuminate\Database\Seeder;

class FollowerTableSeeder extends Seeder
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
                'user_id' => 1,
                'follower_user_id' => 2
            ],
            [
                'user_id' => 1,
                'follower_user_id' => 3
            ],
            [
                'user_id' => 3,
                'follower_user_id' => 2
            ]
        ];

        foreach ($data as $item)
        {
            $follower = new Follower($item);
            $follower->save();
        }
    }
}
