<?php

namespace Tests\Feature;

use App\Models\Commande;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CommandeClientTest extends TestCase
{
    use RefreshDatabase;

    private User $client;
    private User $autreClient;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();

        Role::create(['id' => 1, 'libelle' => 'Administrateur']);
        Role::create(['id' => 2, 'libelle' => 'Client']);
        Role::create(['id' => 3, 'libelle' => 'Employé']);

        $this->client = User::factory()->create([
            'role_id' => 2,
        ]);

        $this->autreClient = User::factory()->create([
            'role_id' => 2,
        ]);
    }

    private function creerCommande(User $user, string $statut = 'En attente'): Commande
    {
        return Commande::create([
            'user_id' => $user->id,
            'date_commande' => now()->toDateString(),
            'date_prestation' => now()->addDays(5)->toDateString(),
            'heure_livraison' => '12:00:00',
            'prix_menu' => 100,
            'nombre_personne' => 5,
            'prix_livraison' => 5,
            'statut' => $statut,
            'pret_materiel' => false,
            'restitution_materiel' => false,
        ]);
    }

    public function test_client_peut_consulter_sa_commande(): void
    {
        $commande = $this->creerCommande($this->client);

        $response = $this
            ->actingAs($this->client)
            ->get(route('client.commandes.show', $commande));

        $response->assertOk();
    }

    public function test_client_ne_peut_pas_consulter_commande_autre_client(): void
    {
        $commande = $this->creerCommande($this->autreClient);

        $response = $this
            ->actingAs($this->client)
            ->get(route('client.commandes.show', $commande));

        $response->assertForbidden();
    }

    public function test_client_ne_peut_pas_modifier_commande_autre_client(): void
    {
        $commande = $this->creerCommande($this->autreClient);

        $response = $this
            ->actingAs($this->client)
            ->put(
                route('client.commandes.update', $commande),
                [
                    'date_prestation' => now()
                        ->addDays(10)
                        ->toDateString(),

                    'heure_livraison' => '13:00',
                    'nombre_personne' => 10,
                ]
            );

        $response->assertForbidden();
    }

    public function test_client_peut_modifier_commande_en_attente(): void
    {
        $commande = $this->creerCommande($this->client);

        $nouvelleDate = now()
            ->addDays(10)
            ->toDateString();

        $response = $this
            ->actingAs($this->client)
            ->put(
                route('client.commandes.update', $commande),
                [
                    'date_prestation' => $nouvelleDate,
                    'heure_livraison' => '14:30',
                    'nombre_personne' => 8,
                    'pret_materiel' => true,
                ]
            );

        $response->assertRedirect(
            route('client.commandes.show', $commande)
        );

        $this->assertDatabaseHas('commandes', [
            'id' => $commande->id,
            'nombre_personne' => 8,
            'pret_materiel' => true,
        ]);
    }

    public function test_commande_acceptee_ne_peut_plus_etre_modifiee(): void
    {
        $commande = $this->creerCommande(
            $this->client,
            'Accepté'
        );

        $response = $this
            ->actingAs($this->client)
            ->put(
                route('client.commandes.update', $commande),
                [
                    'date_prestation' => now()
                        ->addDays(10)
                        ->toDateString(),

                    'heure_livraison' => '14:30',
                    'nombre_personne' => 8,
                ]
            );

        $response->assertSessionHasErrors('commande');
    }

    public function test_client_peut_annuler_commande_en_attente(): void
    {
        $commande = $this->creerCommande($this->client);

        $response = $this
            ->actingAs($this->client)
            ->patch(
                route('client.commandes.cancel', $commande)
            );

        $response->assertRedirect(
            route('client.commandes.index')
        );

        $this->assertDatabaseHas('commandes', [
            'id' => $commande->id,
            'statut' => 'Annulée',
        ]);

        $this->assertDatabaseHas('commande_statuts', [
            'commande_id' => $commande->id,
            'statut' => 'Annulée',
        ]);
    }

    public function test_client_ne_peut_pas_annuler_commande_autre_client(): void
    {
        $commande = $this->creerCommande($this->autreClient);

        $response = $this
            ->actingAs($this->client)
            ->patch(
                route('client.commandes.cancel', $commande)
            );

        $response->assertForbidden();
    }
}