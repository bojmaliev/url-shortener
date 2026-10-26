<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;

class HomePageTest extends DuskTestCase
{
    use DatabaseMigrations;

    #[Test]
    public function it_loads_the_login_page()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertPathIs('/login')
                ->assertSee('URL Shortener');
        });
    }

    #[Test]
    public function it_can_shorten_an_url()
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/links/create')
                ->type('[id="form.url"]', 'http://test-domain-name.tld')
                ->press('Create')
                ->waitForText('Here you go!')
                ->assertPathBeginsWith('/links/');
        });
    }

    #[Test]
    public function it_redirect_a_shorten_url_to_original_url()
    {
        $url = 'https://milon.im/';
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($url, $user) {
            $browser->loginAs($user)
                ->visit('/links/create')
                ->type('[id="form.url"]', $url)
                ->type('[id="form.hash"]', 'milon')
                ->press('Create')
                ->waitForText('Here you go!')
                ->visit('/milon')
                ->assertUrlIs($url);
        });
    }
}
