<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Redis;

class UserSeeder extends Seeder
{
    public function run()
    {
        Redis::flushAll();

        $users = [
            [
                'name' => 'user123',
                'email' => 'user123@test.com',
                'password' => Hash::make('123456'),
            ],
            [
                'name' => 'Michael',
                'email' => 'michael@test.com',
                'password' => Hash::make('123456'),
            ],
            [
                'name' => 'Linda',
                'email' => 'linda@test.com',
                'password' => Hash::make('123456'),
            ],
            [
                'name' => 'Robert',
                'email' => 'robert@test.com',
                'password' => Hash::make('123456'),
            ],
            [
                'name' => 'James',
                'email' => 'james@test.com',
                'password' => Hash::make('123456'),
            ],
            [
                'name' => 'Patricia',
                'email' => 'patricia@test.com',
                'password' => Hash::make('123456'),
            ],
            [
                'name' => 'Barbara',
                'email' => 'barbara@test.com',
                'password' => Hash::make('123456'),
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }

        Artisan::call('app:sync-users-cache');
    }
}
