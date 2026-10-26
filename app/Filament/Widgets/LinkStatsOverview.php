<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Links\LinkResource;
use App\Models\Visitor;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LinkStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $links = LinkResource::getEloquentQuery();

        return [
            Stat::make('Links', $links->clone()->count()),
            Stat::make('Private Links', $links->clone()->where('is_private', true)->count()),
            Stat::make('Visitors', Visitor::query()->whereIn('link_id', $links->clone()->select('id'))->count()),
        ];
    }
}
