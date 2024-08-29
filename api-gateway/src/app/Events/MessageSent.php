<?php

declare(strict_types=1);

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Motyriev\MyDTOLibrary\MessageDTO;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(private MessageDTO $messageDTO)
    {
    }


    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat.' . $this->messageDTO->chatId),
        ];
    }

    public function broadcastWith()
    {
        return ['message' => $this->messageDTO];
    }
}
