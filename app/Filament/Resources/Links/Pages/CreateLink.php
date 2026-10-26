<?php

namespace App\Filament\Resources\Links\Pages;

use App\Contracts\UrlShortenerContract;
use App\Filament\Resources\Links\LinkResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateLink extends CreateRecord
{
    protected static string $resource = LinkResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $link = app(UrlShortenerContract::class)->make($data['url'], $data['hash'] ?? null);

        $link->is_private = $data['is_private'];
        $link->allowed_email = $data['is_private'] ? $data['allowed_email'] : null;
        $link->save();

        return $link;
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Here you go!')
            ->body(url($this->getRecord()->hash));
    }
}
