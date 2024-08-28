<?php

declare(strict_types=1);

namespace App\Queues;

use App\Jobs\AddFriendRequest;
use App\Jobs\ManageFriendRequest;
use VladimirYuldashev\LaravelQueueRabbitMQ\Queue\RabbitMQQueue;

class CustomRabbitMQQueue extends RabbitMQQueue
{
    protected function createPayload($job, $queue, $data = ''): bool|string
    {
        $payload = $this->createPayloadArray($job, $queue, $data);

        if ($job instanceof AddFriendRequest || $job instanceof ManageFriendRequest) {
            $payload['data'] = $job->toArray();
        }

        return json_encode($payload);
    }
}