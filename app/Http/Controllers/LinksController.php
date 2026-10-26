<?php

namespace App\Http\Controllers;

use App\Contracts\UrlShortenerContract;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Jenssegers\Agent\Agent;

class LinksController extends Controller
{
    protected $urlShortener;

    public function __construct(UrlShortenerContract $urlShortener)
    {
        $this->urlShortener = $urlShortener;
    }

    public function process($hash)
    {
        $link = $this->urlShortener->byHash($hash);

        if (! $link) {
            Notification::make()->danger()->title('This URL is non existent')->send();

            return redirect(Filament::getUrl());
        }

        if ($link->is_private) {
            if (! $link->isAllowedByPrivateUser(auth()->user())) {
                Notification::make()->danger()->title('This URL is private. Log in with proper email to access.')->send();

                // Guests come back to this link after logging in.
                return auth()->guest()
                    ? redirect()->guest(Filament::getLoginUrl())
                    : redirect(Filament::getUrl());
            }
        }

        $agent = new Agent;
        $link->visitors()->create([
            'os'      => $agent->platform(),
            'ip'      => request()->ip(),
            'device'  => $agent->device(),
            'browser' => $agent->browser(),
        ]);

        return redirect()->away($link->url);
    }
}
