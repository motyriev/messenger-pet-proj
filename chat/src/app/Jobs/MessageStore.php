<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Jobs\Middleware\LoggerMiddleware;
use App\Services\MessageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Motyriev\MyDTOLibrary\MessageStoreDTO;

class MessageStore implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public MessageStoreDTO $dto)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $messageService = App::make(MessageService::class);
        $messageService->create($this->dto);
    }

    public function middleware()
    {
        return [new LoggerMiddleware($this->dto->traceId)];
    }
}
