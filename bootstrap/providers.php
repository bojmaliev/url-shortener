<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AppPanelProvider;
use App\Providers\UrlShortenerServiceProvider;

return [
    AppServiceProvider::class,
    AppPanelProvider::class,
    UrlShortenerServiceProvider::class,
];
