<?php

namespace App\Providers;

use App\Http\Middleware\CustomAuthentication;
use App\Http\Middleware\WebsocketAuth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Broadcast;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Broadcast::routes(['middleware' => [WebsocketAuth::class]]);

        require base_path('routes/channels.php');
    }
}
