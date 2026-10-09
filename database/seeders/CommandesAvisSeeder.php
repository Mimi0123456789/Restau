<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CommandesAvisSeeder extends Seeder
{
    public function run(): void
    {
        $clients = User::whereHas('role', fn ($q) => $q->where('libelle', 'client'))
            ->where('email', 'like', 'client.%@example.com')
            ->orderBy('id')->get();
        $menus = Menu::orderBy('id')->get();

        if ($clients->isEmpty() || $menus->isEmpty()) {
            throw new RuntimeException('Exécuter UtilisateursSeeder et CatalogueTraiteurSeeder avant ce seeder.');
        }

        $commentaires = [
            'Prestation de démonstration : présentation soignée et service ponctuel.',
            'Avis fictif : les portions étaient généreuses et le menu apprécié.',
            'Avis fictif : livraison conforme et plats bien présentés.',
            'Avis fictif : bonne organisation pour notre événement.',
            'Avis fictif : un repas agréable, quelques ajustements possibles.',
            'Avis fictif : les convives ont apprécié la diversité des plats.',
            'Avis fictif : qualité satisfaisante et équipe réactive.',
            'Avis fictif : belle expérience dans le cadre de ce jeu de test.',
            'Avis fictif : menu intéressant mais livraison un peu tardive.',
            'Avis fictif : prestation correcte, présentation perfectible.',
        ];
        $statuts = ['terminée', 'livrée', 'annulée'];
        $base = Carbon::create(2026, 9, 1, 12, 0, 0);

        DB::transaction(function () use ($clients, $menus, $commentaires, $statuts, $base) {
            for ($i = 1; $i <= 50; $i++) {
                $client = $clients[($i - 1) % $clients->count()];
                $menu = $menus[($i * 7 - 1) % $menus->count()];
                $personnes = max((int) $menu->nombre_personne_minimum, 8 + ($i % 24));
                $unitaire = (float) $menu->prix_par_personne;
                $total = round($personnes * $unitaire, 2);
                $livraison = ($i % 5 === 0) ? 0.0 : 12.0 + ($i % 4) * 4.0;
                $statut = $statuts[($i % 10 === 0) ? 2 : (($i % 3 === 0) ? 1 : 0)];
                $commandeDate = $base->copy()->subDays(130 - $i * 2);
                $prestationDate = $commandeDate->copy()->addDays(7 + ($i % 15));
                $heure = sprintf('%02d:00:00', 11 + ($i % 8));
                $marker = sprintf('DEMO-VG-%03d', $i);

                // La migration commandes ne comporte pas de référence de démonstration.
                // On utilise une table de suivi dédiée pour éviter toute collision avec les vraies commandes.
                $existing = DB::table('demo_commandes_seed')->where('reference', $marker)->first();
                if ($existing) {
                    continue;
                }

                $commandeId = DB::table('commandes')->insertGetId([
                    'user_id' => $client->id,
                    'date_commande' => $commandeDate->toDateString(),
                    'date_prestation' => $prestationDate->toDateString(),
                    'heure_livraison' => $heure,
                    'prix_menu' => $total,
                    'nombre_personne' => $personnes,
                    'prix_livraison' => $livraison,
                    'statut' => $statut,
                    'pret_materiel' => $i % 4 === 0,
                    'restitution_materiel' => $i % 4 === 0 && $statut !== 'annulée',
                    'created_at' => $commandeDate,
                    'updated_at' => $prestationDate,
                ]);

                DB::table('commande_menu')->insert([
                    'commande_id' => $commandeId,
                    'menu_id' => $menu->id,
                    'quantite' => $personnes,
                    'prix_unitaire' => $unitaire,
                    'prix_total' => $total,
                    'created_at' => $commandeDate,
                    'updated_at' => $commandeDate,
                ]);

                foreach (['en attente', 'confirmée', $statut] as $index => $historique) {
                    DB::table('commande_statuts')->insert([
                        'commande_id' => $commandeId,
                        'statut' => $historique,
                        'created_at' => $commandeDate->copy()->addDays($index * 2),
                        'updated_at' => $commandeDate->copy()->addDays($index * 2),
                    ]);
                }

                $note = $statut === 'annulée' ? 2 : (3 + ($i % 3));
                DB::table('avis')->insert([
                    'commande_id' => $commandeId,
                    'note' => $note,
                    'commentaire' => $commentaires[($i - 1) % count($commentaires)],
                    'statut' => $statut === 'annulée' ? 'en attente' : 'validé',
                    'created_at' => $prestationDate->copy()->addDays(1),
                    'updated_at' => $prestationDate->copy()->addDays(1),
                ]);

                DB::table('demo_commandes_seed')->insert([
                    'reference' => $marker,
                    'commande_id' => $commandeId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        $this->command->info('Jeu de démonstration : jusqu’à 50 commandes et 50 avis insérés.');
    }
}
