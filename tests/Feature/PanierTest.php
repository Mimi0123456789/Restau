<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\Regime;
use App\Models\Role;
use App\Models\Theme;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PanierTest extends TestCase
{
    use RefreshDatabase;

    private User $client;
    private Menu $menu;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['id' => 1, 'libelle' => 'Administrateur']);
        Role::create(['id' => 2, 'libelle' => 'Client']);
        Role::create(['id' => 3, 'libelle' => 'Employé']);

        $this->client = User::factory()->create([
            'role_id' => 2,
        ]);

        $regime = Regime::create([
            'libelle' => 'Classique',
        ]);

        $theme = Theme::create([
            'libelle' => 'Traditionnel',
        ]);

        $this->menu = Menu::create([
            'titre' => 'Menu Test',
            'nombre_personne_minimum' => 2,
            'prix_par_personne' => 20,
            'regime_id' => $regime->id,
            'theme_id' => $theme->id,
            'description' => 'Menu utilisé pour les tests.',
            'quantite_restante' => 10,
        ]);
    }

    public function test_client_peut_ajouter_menu_au_panier(): void
    {
        $response = $this
            ->actingAs($this->client)
            ->post(route('panier.add', $this->menu));

        $response->assertSessionHas(
            "panier.{$this->menu->id}.quantite",
            1
        );
    }

    public function test_ajouter_deux_fois_menu_augmente_quantite(): void
    {
        $this->actingAs($this->client)
            ->post(route('panier.add', $this->menu));

        $response = $this
            ->actingAs($this->client)
            ->post(route('panier.add', $this->menu));

        $response->assertSessionHas(
            "panier.{$this->menu->id}.quantite",
            2
        );
    }

    public function test_client_peut_modifier_quantite_panier(): void
    {
        $response = $this
            ->actingAs($this->client)
            ->withSession([
                'panier' => [
                    $this->menu->id => [
                        'quantite' => 1,
                    ],
                ],
            ])
            ->patch(
                route('panier.update', $this->menu),
                ['quantite' => 4]
            );

        $response->assertRedirect(route('panier.index'));

        $response->assertSessionHas(
            "panier.{$this->menu->id}.quantite",
            4
        );
    }

    public function test_quantite_panier_ne_peut_pas_etre_inferieure_a_un(): void
    {
        $response = $this
            ->actingAs($this->client)
            ->from(route('panier.index'))
            ->patch(
                route('panier.update', $this->menu),
                ['quantite' => 0]
            );

        $response->assertSessionHasErrors('quantite');
    }

    public function test_une_commande_ne_peut_pas_depasser_le_stock_disponible(): void
    {
        $this->menu->update(['quantite_restante' => 1]);

        $response = $this
            ->actingAs($this->client)
            ->withSession([
                'panier' => [
                    $this->menu->id => [
                        'quantite' => 2,
                    ],
                ],
            ])
            ->post(route('panier.checkout'), [
                'date_prestation' => now()->toDateString(),
                'heure_livraison' => '19:30',
                'nombre_personne' => 2,
            ]);

        $response->assertSessionHasErrors('panier');
    }

    public function test_client_peut_supprimer_menu_du_panier(): void
    {
        $response = $this
            ->actingAs($this->client)
            ->withSession([
                'panier' => [
                    $this->menu->id => [
                        'quantite' => 2,
                    ],
                ],
            ])
            ->delete(route('panier.remove', $this->menu));

        $response->assertRedirect(route('panier.index'));

        $response->assertSessionMissing(
            "panier.{$this->menu->id}"
        );
    }

    public function test_le_prix_menu_n_est_pas_multiplie_deux_fois(): void
    {
        $this->actingAs($this->client)
            ->withSession([
                'panier' => [
                    $this->menu->id => [
                        'quantite' => 2,
                    ],
                ],
            ])
            ->post(route('panier.checkout'), [
                'date_prestation' => now()->toDateString(),
                'heure_livraison' => '19:30',
                'nombre_personne' => 2,
            ]);

        $this->assertDatabaseHas('commandes', [
            'user_id' => $this->client->id,
            'prix_menu' => 40.0,
        ]);
    }

    public function test_un_menu_sous_les_quantites_minimums_n_est_pas_affiche_dans_la_home(): void
    {
        $menu = Menu::create([
            'titre' => 'Menu non visible',
            'nombre_personne_minimum' => 4,
            'prix_par_personne' => 25,
            'regime_id' => $this->menu->regime_id,
            'theme_id' => $this->menu->theme_id,
            'description' => 'Menu insuffisant pour affichage.',
            'quantite_restante' => 3,
        ]);

        $response = $this
            ->actingAs($this->client)
            ->get(route('home'));

        $response->assertDontSeeText($menu->titre);
    }

    public function test_annuler_une_commande_reinjecte_les_quantites_de_menus(): void
    {
        $this->menu->update(['quantite_restante' => 8]);

        $commande = \App\Models\Commande::create([
            'user_id' => $this->client->id,
            'date_commande' => now()->toDateString(),
            'date_prestation' => now()->addDay()->toDateString(),
            'heure_livraison' => '19:30',
            'prix_menu' => 40,
            'nombre_personne' => 2,
            'prix_livraison' => 0,
            'statut' => 'En attente',
        ]);

        $commande->menus()->attach($this->menu->id, [
            'quantite' => 2,
            'prix_unitaire' => 20,
            'prix_total' => 40,
        ]);

        $this->actingAs($this->client)
            ->patch(route('client.commandes.cancel', $commande));

        $this->menu->refresh();

        $this->assertSame(10, $this->menu->quantite_restante);
    }

    public function test_panier_vide_ne_permet_pas_acces_validation(): void
    {
        $response = $this
            ->actingAs($this->client)
            ->get(route('panier.checkout.form'));

        $response->assertRedirect(route('panier.index'));
    }
}