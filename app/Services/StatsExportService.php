<?php

namespace App\Services;

use App\Models\Commande;

class StatsExportService
{
    public function exportCommande(Commande $commande): void
    {
        $commande->loadMissing(['menus.theme', 'menus.regime']);

        $menus = $commande->menus;
        $nb = max($menus->count(), 1);
        $livraisonPart = $commande->prix_livraison / $nb;

    }
}