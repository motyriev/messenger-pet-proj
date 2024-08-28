<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Motyriev\MyDTOLibrary\ManageFriendRequestDTO;

class ManageFriendRequest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public ManageFriendRequestDTO $dto)
    {
        Log::info(__METHOD__ . ' job created', ['dto' => $this->dto->toArray()]);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
    }

    public function toArray(): array
    {
        return $this->dto->toArray();
    }
}
