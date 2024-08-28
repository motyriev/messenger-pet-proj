<?php

namespace Database\Seeders;

use App\Models\Chat;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Artisan;

class MessageSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        $chats = [
            [
                'user_1' => 1,
                'user_2' => 2,
            ],
            [
                'user_1' => 1,
                'user_2' => 3,
            ],
            [
                'user_1' => 1,
                'user_2' => 4,
            ],
        ];

        foreach ($chats as $chatData) {
            $chat = Chat::create($chatData);

            $chatCreatedAt = $faker->dateTimeBetween('-1 year', 'now');
            $chat->created_at = $chatCreatedAt;
            $chat->updated_at = $chatCreatedAt;
            $chat->save();

            $currentMessageTime = clone $chatCreatedAt;

            for ($i = 0; $i < rand(1, 20); $i++) {
                $currentMessageTime = $faker->dateTimeBetween($currentMessageTime, '+1 day');

                $chat->messages()->create([
                    'user_id' => $faker->randomElement([$chatData['user_1'], $chatData['user_2']]),
                    'body' => $faker->sentence(rand(2, 4)),
                    'created_at' => $currentMessageTime,
                    'updated_at' => $currentMessageTime,
                ]);
            }
        }

        Artisan::call('app:sync-last-messages');
    }
}