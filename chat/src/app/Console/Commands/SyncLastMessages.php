<?php

namespace App\Console\Commands;

use App\Models\Chat;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class SyncLastMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-last-messages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $chats = Chat::with('lastMessage')->get();

        foreach ($chats as $chat) {
            Redis::hmSet('chat:' . $chat->id . ':last_message', ['body' => $chat->lastMessage->body]);
        }
    }
}