<?php

namespace App\Filament\Resources\Links\Tables;

use App\Models\Link;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class LinksTable
{
    public static function configure(Table $table): Table
    {
        $isAdmin = fn (): bool => auth()->user()->is_admin;

        return $table
            ->columns([
                TextColumn::make('url')
                    ->label('URL')
                    ->limit(60)
                    ->searchable(),
                TextColumn::make('short_link')
                    ->label('Shorten Link')
                    ->state(fn (Link $record): string => url($record->hash))
                    ->copyable()
                    ->searchable(['hash']),
                TextColumn::make('user.name')
                    ->label('User')
                    ->placeholder('-')
                    ->visible($isAdmin),
                IconColumn::make('is_private')
                    ->label('Is Private')
                    ->boolean(),
                TextColumn::make('visitors_count')
                    ->label('Visitors')
                    ->counts('visitors')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->since()
                    ->dateTimeTooltip()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TernaryFilter::make('is_private')
                    ->label('Is Private'),
                SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->visible($isAdmin),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->emptyStateHeading('No links yet!');
    }
}
