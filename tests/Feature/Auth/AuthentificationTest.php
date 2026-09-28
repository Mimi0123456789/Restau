<?php

namespace Tests\Feature\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthentificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Création des rôles nécessaires aux tests.
     */
    protected function setUp(): void
    {
        parent::setUp();

        Role::create([
            'id' => 1,
            'libelle' => 'Administrateur',
        ]);

        Role::create([
            'id' => 2,
            'libelle' => 'Client',
        ]);

        Role::create([
            'id' => 3,
            'libelle' => 'Employé',
        ]);
    }

    /**
     * Vérifie que la page de connexion est accessible.
     */
    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    /**
     * Vérifie qu'un utilisateur peut se connecter
     * avec des identifiants valides.
     */
    public function test_user_can_login(): void
    {
        $user = User::factory()->create([
            'prenom' => 'Jean',
            'nom' => 'Dupont',
            'email' => 'jean.dupont@test.fr',
            'password' => Hash::make('Password123!'),
            'role_id' => 2,
            'is_active' => true,
        ]);

        $response = $this->post('/login', [
            'email' => 'jean.dupont@test.fr',
            'password' => 'Password123!',
        ]);

        // Vérifie que l'utilisateur est connecté
        $this->assertAuthenticatedAs($user);

        // Un client doit arriver sur son dashboard
        $response->assertRedirect(route('dashboard.client'));
    }

    /**
     * Vérifie qu'un utilisateur ne peut pas se connecter
     * avec un mot de passe incorrect.
     */
    public function test_user_cannot_login_with_invalid_password(): void
    {
        User::factory()->create([
            'prenom' => 'Jean',
            'nom' => 'Dupont',
            'email' => 'jean.dupont@test.fr',
            'password' => Hash::make('Password123!'),
            'role_id' => 2,
        ]);

        $response = $this->post('/login', [
            'email' => 'jean.dupont@test.fr',
            'password' => 'mauvaisMotDePasse',
        ]);

        $this->assertGuest();

        $response->assertSessionHasErrors('email');
    }

    /**
     * Vérifie qu'un utilisateur connecté peut se déconnecter.
     */
    public function test_user_can_logout(): void
    {
        $user = User::factory()->create([
            'prenom' => 'Jean',
            'nom' => 'Dupont',
            'email' => 'jean.dupont@test.fr',
            'password' => Hash::make('Password123!'),
            'role_id' => 2,
        ]);

        $response = $this
            ->actingAs($user)
            ->post('/logout');

        $this->assertGuest();

        $response->assertRedirect('/');
    }
}