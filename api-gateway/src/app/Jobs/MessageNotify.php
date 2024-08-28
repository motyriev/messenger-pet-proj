<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Events\MessageSent;
use Illuminate\Support\Facades\Log;
use Motyriev\MyDTOLibrary\MessageDTO;

class MessageNotify implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public MessageDTO $dto)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info(__METHOD__ . ' job started', ['dto' => $this->dto->toArray()]);

        try {
            event(new MessageSent($this->dto));
            Log::info(__METHOD__ . ' job finished successfully', ['dto' => $this->dto->toArray()]);
        } catch (\Exception $e) {
            Log::error(__METHOD__ . ' job failed', [
                'dto'   => $this->dto->toArray(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
