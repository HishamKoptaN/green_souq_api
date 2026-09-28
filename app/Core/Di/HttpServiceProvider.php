<?php

namespace App\Core\Di;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

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
