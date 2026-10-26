<?php

namespace App\Filament\Pages\Auth;

use Filament\Actions\Action;
use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontFamily;
use Illuminate\Support\Str;

class EditProfile extends BaseEditProfile
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getNameFormComponent(),
                TextInput::make('email')
                    ->label(__('filament-panels::auth/pages/edit-profile.form.email.label'))
                    ->disabled(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
                $this->getCurrentPasswordFormComponent(),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getFormContentComponent(),
                $this->getApiTokenContentComponent(),
            ]);
    }

    protected function getApiTokenContentComponent(): Component
    {
        return Section::make('API Token')
            ->key('apiToken')
            ->description('Send it as a Bearer token in the Authorization header of API requests.')
            ->schema([
                TextEntry::make('api_token')
                    ->hiddenLabel()
                    ->state(fn (): ?string => $this->getUser()->api_token)
                    ->fontFamily(FontFamily::Mono)
                    ->copyable(),
            ])
            ->footer([
                Action::make('generateToken')
                    ->label('Generate New API Token')
                    ->requiresConfirmation()
                    ->modalDescription('Generating new API key will invalidate old API key. Make sure you want to do that.')
                    ->action(function (): void {
                        $user = $this->getUser();
                        $user->api_token = Str::random(30);
                        $user->save();

                        Notification::make()
                            ->success()
                            ->title('New API token created successfully.')
                            ->send();
                    }),
            ]);
    }
}
