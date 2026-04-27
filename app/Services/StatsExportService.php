<?php

namespace App\Services;

use App\Models\Commande;
use App\Models\Mongo\OrderMenuStat;

class StatsExportService
{
    public function exportCommande(Commande $commande): void
    {
        $commande->loadMissing(['menus.theme', 'menus.regime']);

        $menus = $commande->menus;
        $nb = max($menus->count(), 1);
        $livraisonPart = $commande->prix_livraison / $nb;

        foreach ($menus as $menu) {
            OrderMenuStat::updateOrCreate(
                [
                    'commande_id' => $commande->id,
                    'menu_id' => $menu->id,
                ],
                [
                    'menu_titre' => $menu->titre,
                    'user_id' => $commande->user_id,

                    'date_commande' => optional($commande->date_commande)?->format('Y-m-d'),
                    'date_prestation' => optional($commande->date_prestation)?->format('Y-m-d'),

                    'statut' => $commande->statut,

                    'nombre_personne' => $commande->nombre_personne,
                    'quantite' => $menu->pivot->quantite,

                    'prix_unitaire' => $menu->pivot->prix_unitaire,
                    'prix_total' => $menu->pivot->prix_total,

                    'prix_livraison_repartie' => $livraisonPart,
                    'chiffre_affaires_ligne' => $menu->pivot->prix_total + $livraisonPart,

                    'theme_libelle' => optional($menu->theme)->libelle,
                    'regime_libelle' => optional($menu->regime)->libelle,

                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}