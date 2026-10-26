<?php

namespace Tests\Feature;

use App\Contracts\UrlShortenerContract;
use App\Filament\Pages\Auth\EditProfile;
use App\Filament\Resources\Links\Pages\CreateLink;
use App\Filament\Resources\Links\Pages\ListLinks;
use App\Filament\Resources\Links\Pages\ViewLink;
use App\Models\Link;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PanelTest extends TestCase
{
    use RefreshDatabase;

    protected function linkFor(User $user, string $url): Link
    {
        $this->actingAs($user);

        return app(UrlShortenerContract::class)->make($url);
    }

    #[Test]
    public function guests_are_redirected_to_login()
    {
        $this->get('/')->assertRedirect('/login');
        $this->get('/links')->assertRedirect('/login');
    }

    #[Test]
    public function it_shows_the_dashboard()
    {
        $this->actingAs(User::factory()->create())
            ->get('/')
            ->assertOk()
            ->assertSee('Shorten URL');
    }

    #[Test]
    public function it_can_shorten_an_url()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(CreateLink::class)
            ->fillForm([
                'url' => 'https://milon.im/',
                'hash' => 'milon',
            ])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertNotified('Here you go!');

        $this->assertDatabaseHas('links', [
            'url' => 'https://milon.im/',
            'hash' => 'milon',
            'user_id' => $user->id,
            'is_private' => false,
            'allowed_email' => null,
        ]);
    }

    #[Test]
    public function it_can_shorten_a_private_url()
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(CreateLink::class)
            ->fillForm([
                'url' => 'https://milon.im/',
                'is_private' => true,
                'allowed_email' => 'contact@milon.im, test@gmail.de',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('links', [
            'url' => 'https://milon.im/',
            'is_private' => true,
            'allowed_email' => 'contact@milon.im, test@gmail.de',
        ]);
    }

    #[Test]
    public function it_validates_the_link_form()
    {
        $this->linkFor(User::factory()->create(), 'https://laravel.com/')->update(['hash' => 'taken']);

        Livewire::test(CreateLink::class)
            ->fillForm([
                'url' => 'not a url',
                'hash' => 'taken',
                'is_private' => true,
            ])
            ->call('create')
            ->assertHasFormErrors([
                'url' => 'url',
                'hash' => 'unique',
                'allowed_email' => 'required',
            ]);

        Livewire::test(CreateLink::class)
            ->fillForm([
                'url' => 'https://milon.im/',
                'is_private' => true,
                'allowed_email' => 'contact@milon.im, not-an-email',
            ])
            ->call('create')
            ->assertHasFormErrors(['allowed_email']);
    }

    #[Test]
    public function users_only_see_their_own_links()
    {
        $other = $this->linkFor(User::factory()->create(), 'https://laravel.com/');
        $own = $this->linkFor($user = User::factory()->create(), 'https://milon.im/');

        Livewire::test(ListLinks::class)
            ->assertCanSeeTableRecords([$own])
            ->assertCanNotSeeTableRecords([$other]);

        $this->get('/links/'.$own->id)->assertOk()->assertSee(url($own->hash));
        $this->get('/links/'.$other->id)->assertNotFound();
    }

    #[Test]
    public function admins_see_all_links_and_users()
    {
        $first = $this->linkFor(User::factory()->create(), 'https://laravel.com/');
        $second = $this->linkFor(User::factory()->create(), 'https://milon.im/');

        $this->actingAs(User::factory()->create(['is_admin' => true]));

        Livewire::test(ListLinks::class)->assertCanSeeTableRecords([$first, $second]);
        Livewire::test(ViewLink::class, ['record' => $first->getRouteKey()])->assertOk();

        $this->get('/users')->assertOk();
    }

    #[Test]
    public function only_admins_can_see_users()
    {
        $this->actingAs(User::factory()->create())
            ->get('/users')
            ->assertForbidden();
    }

    #[Test]
    public function it_can_generate_a_new_api_token()
    {
        $user = User::factory()->create();
        $oldToken = $user->api_token;

        $this->actingAs($user)->get('/profile')->assertOk()->assertSee($oldToken);

        Livewire::test(EditProfile::class)
            ->callAction(TestAction::make('generateToken')->schemaComponent('apiToken', schema: 'content'))
            ->assertNotified('New API token created successfully.');

        $this->assertNotSame($oldToken, $user->fresh()->api_token);
    }
}
