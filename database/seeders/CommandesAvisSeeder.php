<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class CommandesAvisSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['users', 'roles', 'menus', 'commandes', 'commande_menu', 'commande_statuts', 'avis'] as $table) {
            if (!Schema::hasTable($table)) {
                throw new RuntimeException("Table absente : {$table}");
            }
        }

        $clients = DB::table('users')
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->where('roles.libelle', 'client')
            ->where('users.email', 'like', 'client.%@example.com')
            ->select('users.id')
            ->orderBy('users.id')
            ->pluck('users.id')
            ->all();

        $menus = DB::table('menus')->select('id', 'prix_par_personne', 'nombre_personne_minimum')
            ->orderBy('id')->get()->all();

        if (!$clients || !$menus) {
            throw new RuntimeException('Il faut des clients fictifs client.%@example.com et des menus existants avant ce seeder.');
        }

        // Identifiant de démonstration stocké dans le commentaire des avis.
        // Permet de reconnaître uniquement les données créées par ce seeder,
        // sans créer de table supplémentaire.
        $prefix = '[DEMO-VG-2026-';
        $existing = DB::table('avis')->where('commentaire', 'like', $prefix . '%')->count();
        if ($existing > 0) {
            $this->command->warn("{$existing} avis de ce jeu de démonstration existent déjà. Aucune insertion pour éviter les doublons.");
            return;
        }

        $commentaires = [
            'Très bonne prestation, présentation soignée et service ponctuel.',
            'Repas apprécié par tous les invités, portions généreuses.',
            'Cuisine savoureuse et livraison à l’heure.',
            'Très bon rapport qualité-prix pour notre événement.',
            'Menu varié, produits frais et équipe professionnelle.',
            'Bonne organisation, les plats ont été très appréciés.',
            'Expérience satisfaisante, nous recommanderons ce traiteur.',
            'Les desserts ont particulièrement plu à nos convives.',
            'Service agréable et cuisine de qualité.',
            'Prestation correcte, quelques ajustements possibles.',
            'Très belle présentation des plats et saveurs équilibrées.',
            'Commande conforme et personnel réactif.',
            'Les invités ont apprécié le choix proposé.',
            'Livraison bien organisée et repas chaleureux.',
            'Belle expérience pour notre repas de famille.',
        ];
        $notes = [5, 4, 5, 4, 5, 4, 3, 5, 4, 5, 4, 5, 3, 4, 5];
        $statutsAvis = ['valide', 'valide', 'valide', 'en attente', 'valide'];
        // Ajuster les libellés ci-dessus et ci-dessous si tes contrôleurs utilisent d'autres valeurs.

        DB::transaction(function () use ($clients, $menus, $commentaires, $notes, $statutsAvis, $prefix) {
            for ($i = 0; $i < 50; $i++) {
                $clientId = $clients[$i % count($clients)];
                $menu = $menus[($i * 7 + intdiv($i, 7)) % count($menus)];
                $personnes = max((int) $menu->nombre_personne_minimum, 6) + ($i % 5) * 2;
                $prixUnitaire = (float) $menu->prix_par_personne;
                $totalMenu = round($prixUnitaire * $personnes, 2);
                $livraison = [0, 12, 18, 25][$i % 4];
                $prestation = Carbon::today()->subDays(12 + ($i * 4));
                $commande = $prestation->copy()->subDays(7 + ($i % 15));
                $createdAt = $commande->copy()->setTime(10 + ($i % 7), 15);
                $livreeAt = $prestation->copy()->setTime(18, 0);
                $avisAt = $prestation->copy()->addDays(1 + ($i % 4))->setTime(11, 0);
                $avecMateriel = $i % 4 === 0;

                $commandeId = DB::table('commandes')->insertGetId([
                    'user_id' => $clientId,
                    'date_commande' => $commande->toDateString(),
                    'date_prestation' => $prestation->toDateString(),
                    'heure_livraison' => sprintf('%02d:00:00', 11 + ($i % 8)),
                    'prix_menu' => $totalMenu,
                    'nombre_personne' => $personnes,
                    'prix_livraison' => $livraison,
                    'statut' => 'terminee',
                    'pret_materiel' => $avecMateriel,
                    'restitution_materiel' => $avecMateriel,
                    'created_at' => $createdAt,
                    'updated_at' => $livreeAt,
                ]);

                DB::table('commande_menu')->insert([
                    'commande_id' => $commandeId,
                    'menu_id' => $menu->id,
                    'quantite' => $personnes,
                    'prix_unitaire' => $prixUnitaire,
                    'prix_total' => $totalMenu,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);

                foreach ([
                    ['en attente', $createdAt],
                    ['confirmee', $commande->copy()->addDay()->setTime(9, 0)],
                    ['terminee', $livreeAt],
                ] as [$statut, $date]) {
                    DB::table('commande_statuts')->insert([
                        'commande_id' => $commandeId,
                        'statut' => $statut,
                        'created_at' => $date,
                        'updated_at' => $date,
                    ]);
                }

                DB::table('avis')->insert([
                    'commande_id' => $commandeId,
                    'note' => $notes[$i % count($notes)],
                    'commentaire' => sprintf('%s%03d] ', $prefix, $i + 1) . $commentaires[$i % count($commentaires)],
                    'statut' => $statutsAvis[$i % count($statutsAvis)],
                    'created_at' => $avisAt,
                    'updated_at' => $avisAt,
                ]);
            }
        });

        $this->command->info('50 commandes, 50 lignes commande_menu, 150 historiques de statut et 50 avis de démonstration insérés.');
    }
}
