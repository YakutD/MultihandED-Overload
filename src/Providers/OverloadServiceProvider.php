<?php

namespace MultihandED\Overload\Providers;

use MultihandED\Overload\Console\Commands\OverloadCreate;
use Illuminate\Support\ServiceProvider;

class OverloadServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                OverloadCreate::class
            ]);
        }
    }

    public function register()
    {
    }
}
