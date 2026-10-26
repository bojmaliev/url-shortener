<?php

namespace App\Filament\Resources\Links\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Spatie\ValidationRules\Rules\Delimited;

class LinkForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                TextInput::make('url')
                    ->label('Long URL')
                    ->placeholder('Long URL goes here...')
                    ->required()
                    ->url()
                    ->autofocus(),
                TextInput::make('hash')
                    ->label('Custom Hash')
                    ->placeholder('Put your custom hash name (Optional)')
                    ->unique(),
                Toggle::make('is_private')
                    ->label('Is Private')
                    ->live(),
                TextInput::make('allowed_email')
                    ->label('Allowed Emails')
                    ->placeholder('Comma separated emails')
                    ->visible(fn (Get $get): bool => (bool) $get('is_private'))
                    ->required()
                    ->rule(new Delimited('email')),
            ]);
    }
}
