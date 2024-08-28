<?php

namespace App\Providers;

use App\Services\DashboardService;
use FriendsService\FriendsServiceClient;
use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;

class DashboardServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        $this->app->bind(DashboardService::class, function ($app) {
            $friendsServiceClient = new FriendsServiceClient(config('app.urls.friends_grpc'), ['credentials' => \Grpc\ChannelCredentials::createInsecure()]);
            $httpClient = new Client(['timeout' => 10.0]);

            return new DashboardService($friendsServiceClient, $httpClient);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
