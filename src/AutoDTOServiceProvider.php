<?php

namespace AutoDTO\Helper;

use AutoDTO\Helper\Http\Middleware\AutoDTOBinderMiddleware;
use Illuminate\Support\ServiceProvider;

class AutoDTOServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app['router']->aliasMiddleware('autoDTO.hook', AutoDTOBinderMiddleware::class);
    }
}
