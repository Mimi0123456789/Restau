<?php

namespace Tests\Feature;

use App\Models\Avis;
use App\Models\Commande;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AvisClientTest extends TestCase
{
    use RefreshDatabase;

    private User $client;
    private User $autreClient;

    protected function setUp(): void
    {
        parent::setUp();

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

    private function creerCommande(
        User $user,
        string $statut = 'Terminée'
    ): Commande {
        return Commande::create([
            'user_id' => $user->id,
            'date_commande' => now()->toDateString(),
            'date_prestation' => now()->subDay()->toDateString(),
            'heure_livraison' => '12:00:00',
            'prix_menu' => 100,
            'nombre_personne' => 5,
            'prix_livraison' => 5,
            'statut' => $statut,
            'pret_materiel' => false,
            'restitution_materiel' => false,
        ]);
    }

    public function test_client_peut_deposer_avis_sur_commande_terminee(): void
    {
        $commande = $this->creerCommande($this->client);

        $response = $this
            ->actingAs($this->client)
            ->post(
                route('client.commandes.avis.store', $commande),
                [
                    'note' => 5,
                    'commentaire' => 'Très bonne prestation.',
                ]
            );

        $response->assertRedirect(
            route('client.commandes.show', $commande)
        );

        $this->assertDatabaseHas('avis', [
            'commande_id' => $commande->id,
            'note' => 5,
            'commentaire' => 'Très bonne prestation.',
            'statut' => 'en attente',
        ]);
    }

    public function test_client_ne_peut_pas_deposer_avis_avant_fin_commande(): void
    {
        $commande = $this->creerCommande(
            $this->client,
            'En préparation'
        );

        $response = $this
            ->actingAs($this->client)
            ->post(
                route('client.commandes.avis.store', $commande),
                [
                    'note' => 4,
                    'commentaire' => 'Test',
                ]
            );

        $response->assertSessionHasErrors('avis');

        $this->assertDatabaseMissing('avis', [
            'commande_id' => $commande->id,
        ]);
    }

    public function test_note_doit_etre_comprise_entre_un_et_cinq(): void
    {
        $commande = $this->creerCommande($this->client);

        $response = $this
            ->actingAs($this->client)
            ->post(
                route('client.commandes.avis.store', $commande),
                [
                    'note' => 6,
                    'commentaire' => 'Note invalide',
                ]
            );

        $response->assertSessionHasErrors('note');

        $this->assertDatabaseMissing('avis', [
            'commande_id' => $commande->id,
        ]);
    }

    public function test_client_ne_peut_deposer_qu_un_avis_par_commande(): void
    {
        $commande = $this->creerCommande($this->client);

        Avis::create([
            'commande_id' => $commande->id,
            'note' => 5,
            'commentaire' => 'Premier avis',
            'statut' => 'en attente',
        ]);

        $response = $this
            ->actingAs($this->client)
            ->post(
                route('client.commandes.avis.store', $commande),
                [
                    'note' => 4,
                    'commentaire' => 'Deuxième avis',
                ]
            );

        $response->assertSessionHasErrors('avis');

        $this->assertEquals(
            1,
            Avis::where('commande_id', $commande->id)->count()
        );
    }

    public function test_client_ne_peut_pas_noter_commande_autre_client(): void
    {
        $commande = $this->creerCommande($this->autreClient);

        $response = $this
            ->actingAs($this->client)
            ->post(
                route('client.commandes.avis.store', $commande),
                [
                    'note' => 5,
                    'commentaire' => 'Tentative interdite',
                ]
            );

        $response->assertForbidden();

        $this->assertDatabaseMissing('avis', [
            'commande_id' => $commande->id,
        ]);
    }
}