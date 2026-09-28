<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['id' => 1, 'libelle' => 'Administrateur']);
        Role::create(['id' => 2, 'libelle' => 'Client']);
        Role::create(['id' => 3, 'libelle' => 'Employé']);
    }

    public function test_visiteur_est_redirige_vers_connexion(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_client_ne_peut_pas_acceder_dashboard_admin(): void
    {
        $client = User::factory()->create([
            'role_id' => 2,
        ]);

        $response = $this
            ->actingAs($client)
            ->get(route('admin.dashboard'));

        $response->assertForbidden();
    }

    public function test_employe_ne_peut_pas_acceder_dashboard_admin(): void
    {
        $employe = User::factory()->create([
            'role_id' => 3,
        ]);

        $response = $this
            ->actingAs($employe)
            ->get(route('admin.dashboard'));

        $response->assertForbidden();
    }

    public function test_admin_peut_acceder_dashboard_admin(): void
    {
        $admin = User::factory()->create([
            'role_id' => 1,
        ]);

        $response = $this
            ->actingAs($admin)
            ->get(route('admin.dashboard'));

        $response->assertOk();
    }

    public function test_client_ne_peut_pas_acceder_gestion_commandes(): void
    {
        $client = User::factory()->create([
            'role_id' => 2,
        ]);

        $response = $this
            ->actingAs($client)
            ->get(route('commandes.index'));

        $response->assertForbidden();
    }

    public function test_employe_peut_acceder_gestion_commandes(): void
    {
        $employe = User::factory()->create([
            'role_id' => 3,
        ]);

        $response = $this
            ->actingAs($employe)
            ->get(route('commandes.index'));

        $response->assertOk();
    }
}