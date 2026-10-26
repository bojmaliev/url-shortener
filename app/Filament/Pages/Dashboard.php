<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Links\LinkResource;
use Filament\Actions\Action;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected function getHeaderActions(): array
    {
        return [
            Action::make('shorten')
                ->label('Shorten URL')
                ->url(LinkResource::getUrl('create')),
        ];
    }
}
