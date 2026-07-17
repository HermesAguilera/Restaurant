<?php

namespace Tests\Feature;

use App\Filament\Pages\CustomLogin;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_login_page_can_be_rendered()
    {
        $response = $this->get('/admin/login');
        $response->assertStatus(200);
    }

    public function test_user_can_login()
    {
        $user = User::factory()->create([
            'password' => bcrypt($password = 'password'),
        ]);

        // User::canAccessPanel() exige al menos un rol: sin esto Filament
        // desloguea al usuario y devuelve un error de credenciales. Se usa 'root'
        // porque el Gate::before de AppServiceProvider le da bypass, evitando que
        // la navegación consulte permisos que este test no siembra.
        $user->assignRole(Role::create(['name' => 'root', 'guard_name' => 'web']));

        // El login de Filament es un componente Livewire, no un POST a /admin/login.
        Livewire::test(CustomLogin::class)
            ->fillForm([
                'email' => $user->email,
                'password' => $password,
            ])
            ->call('authenticate')
            ->assertHasNoFormErrors();

        $this->assertAuthenticatedAs($user);
    }
}
