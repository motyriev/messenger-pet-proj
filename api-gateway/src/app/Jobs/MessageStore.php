<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Motyriev\MyDTOLibrary\MessageStoreDTO;

class MessageStore implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public MessageStoreDTO $dto)
    {
        Log::info(__METHOD__ . ' job created', ['dto' => $this->dto->toArray()]);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {}
}
