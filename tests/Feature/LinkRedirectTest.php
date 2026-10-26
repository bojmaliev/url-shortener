<?php

namespace Tests\Feature;

use App\Contracts\UrlShortenerContract;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LinkRedirectTest extends TestCase
{
    use RefreshDatabase;

    protected $urlShortener;

    protected function setUp(): void
    {
        parent::setUp();

        $this->urlShortener = app(UrlShortenerContract::class);
    }

    #[Test]
    public function it_redirects_a_shorten_url_to_the_original_url()
    {
        $link = $this->urlShortener->make('https://milon.im/');

        $this->get('/'.$link->hash)->assertRedirect('https://milon.im/');

        $this->assertSame(1, $link->visitors()->count());
    }

    #[Test]
    public function it_redirects_home_for_an_unknown_hash()
    {
        $this->get('/unknown')->assertRedirect(Filament::getUrl());
    }

    #[Test]
    public function it_sends_guests_to_login_for_a_private_link()
    {
        $link = $this->urlShortener->make('https://milon.im/');
        $link->is_private = true;
        $link->allowed_email = 'allowed@example.com';
        $link->save();

        $this->get('/'.$link->hash)
            ->assertRedirect(Filament::getLoginUrl())
            ->assertSessionHas('url.intended', url($link->hash));

        $this->assertSame(0, $link->visitors()->count());
    }

    #[Test]
    public function it_redirects_an_allowed_user_to_a_private_link()
    {
        $link = $this->urlShortener->make('https://milon.im/');
        $link->is_private = true;
        $link->allowed_email = 'other@example.com, allowed@example.com';
        $link->save();

        $allowed = User::factory()->create(['email' => 'allowed@example.com']);
        $notAllowed = User::factory()->create();

        $this->actingAs($notAllowed)->get('/'.$link->hash)->assertRedirect(Filament::getUrl());
        $this->actingAs($allowed)->get('/'.$link->hash)->assertRedirect('https://milon.im/');
    }

    #[Test]
    public function panel_routes_are_not_treated_as_hashes()
    {
        $this->get('/login')->assertOk();
    }
}
