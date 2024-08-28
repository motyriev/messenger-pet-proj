<?php

namespace App\Providers;

use App\Jobs\MessageStore;
use App\Repositories\Contracts\MessageRepositoryInterface;
use App\Repositories\Eloquent\MessageRepository;
use App\Services\MessageService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(RepositoryServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        App::bindMethod(MessageStore::class . '@handle', fn($job) => $job->handle(new MessageService(App::make(MessageRepositoryInterface::class))));
    }
}
