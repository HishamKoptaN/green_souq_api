<?php

namespace App\Core\Di;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Http;

class HttpServiceProvider extends ServiceProvider
{
    public function register()
    {
        Http::macro('salla', function () {
            return Http::withHeaders([
                'Accept' => 'application/json',
                'Accept-Language' => 'ar',
            ])->timeout(5);
        });
    }
}
