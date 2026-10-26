<?php

namespace App\Filament\Resources\Links\Schemas;

use App\Models\Link;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class LinkInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('url')
                    ->label('URL')
                    ->url(fn (Link $record): string => $record->url, shouldOpenInNewTab: true)
                    ->columnSpanFull(),
                TextEntry::make('short_link')
                    ->label('Shorten Link')
                    ->state(fn (Link $record): string => url($record->hash))
                    ->copyable(),
                TextEntry::make('created_at')
                    ->label('Created At')
                    ->since()
                    ->dateTimeTooltip(),
                IconEntry::make('is_private')
                    ->label('Is Private')
                    ->boolean(),
                TextEntry::make('allowed_email_label')
                    ->label('Allowed Emails'),
                TextEntry::make('user.name')
                    ->label('User')
                    ->placeholder('-')
                    ->visible(fn (): bool => auth()->user()->is_admin),
            ]);
    }
}
