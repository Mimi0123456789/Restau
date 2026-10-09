<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        return view('dashboard.admin.index');
    }

    public function statistiques(Request $request)
    {
        $menus = Menu::orderBy('titre')->get();

        $menuId = $request->get('menu_id');
        $dateDebut = $request->get('date_debut');
        $dateFin = $request->get('date_fin');

        $commandesQuery = Commande::with(['user', 'menus'])
            ->when($menuId, function ($query) use ($menuId) {
                $query->whereHas('menus', function ($q) use ($menuId) {
                    $q->where('menus.id', $menuId);
                });
            })
            ->when($dateDebut, function ($query) use ($dateDebut) {
                $query->whereDate('date_commande', '>=', $dateDebut);
            })
            ->when($dateFin, function ($query) use ($dateFin) {
                $query->whereDate('date_commande', '<=', $dateFin);
            });

        $query = DB::table('commande_menu')
            ->join('menus', 'menus.id', '=', 'commande_menu.menu_id')
            ->join('commandes', 'commandes.id', '=', 'commande_menu.commande_id');

        if (!empty($menuId)) {
            $query->where('menus.id', $menuId);
        }

        if (!empty($dateDebut)) {
            $query->whereDate('commandes.date_commande', '>=', $dateDebut);
        }

        if (!empty($dateFin)) {
            $query->whereDate('commandes.date_commande', '<=', $dateFin);
        }

        $statsMenus = $query
            ->select(
                'menus.id',
                'menus.titre',
                DB::raw('COUNT(DISTINCT commandes.id) as total_commandes'),
                DB::raw('SUM(commande_menu.quantite) as total_quantite'),
                DB::raw('SUM(commande_menu.prix_total) as chiffre_affaires')
            )
            ->groupBy('menus.id', 'menus.titre')
            ->orderByDesc('chiffre_affaires')
            ->get();

        $summary = (clone $commandesQuery)
            ->select(
                DB::raw('COUNT(*) as total_commandes'),
                DB::raw('COALESCE(SUM(prix_menu + prix_livraison), 0) as chiffre_affaires_total'),
                DB::raw('COALESCE(SUM(prix_livraison), 0) as chiffre_affaires_livraison'),
                DB::raw('COALESCE(AVG(prix_menu + prix_livraison), 0) as panier_moyen')
            )
            ->first();

        $statusCounts = (clone $commandesQuery)
            ->select('statut', DB::raw('COUNT(*) as total'))
            ->groupBy('statut')
            ->orderByDesc('total')
            ->get();

        $recentCommandes = (clone $commandesQuery)
            ->latest('date_commande')
            ->limit(8)
            ->get();

        $dailyRevenue = (clone $commandesQuery)
            ->select(
                DB::raw('DATE(date_commande) as date_commande'),
                DB::raw('SUM(prix_menu + prix_livraison) as ca_journalier')
            )
            ->groupBy(DB::raw('DATE(date_commande)'))
            ->orderBy('date_commande')
            ->get();

        return view('dashboard.admin.statistiques', [
            'menus' => $menus,
            'menuId' => $menuId,
            'dateDebut' => $dateDebut,
            'dateFin' => $dateFin,
            'statsMenus' => $statsMenus,
            'totalCommandes' => (int) ($summary->total_commandes ?? 0),
            'totalChiffreAffaires' => (float) ($summary->chiffre_affaires_total ?? 0),
            'totalLivraison' => (float) ($summary->chiffre_affaires_livraison ?? 0),
            'panierMoyen' => (float) ($summary->panier_moyen ?? 0),
            'statusCounts' => $statusCounts,
            'recentCommandes' => $recentCommandes,
            'dailyRevenue' => $dailyRevenue,
            'totalQuantite' => $statsMenus->sum('total_quantite'),
            'commandesParMenuLabels' => $statsMenus->pluck('titre')->values(),
            'commandesParMenuData' => $statsMenus->pluck('total_commandes')->map(fn ($v) => (int) $v)->values(),
            'quantitesParMenuLabels' => $statsMenus->pluck('titre')->values(),
            'quantitesParMenuData' => $statsMenus->pluck('total_quantite')->map(fn ($v) => (int) $v)->values(),
            'caParMenuLabels' => $statsMenus->pluck('titre')->values(),
            'caParMenuData' => $statsMenus->pluck('chiffre_affaires')->map(fn ($v) => round((float) $v, 2))->values(),
            'statusLabels' => $statusCounts->pluck('statut')->values(),
            'statusData' => $statusCounts->pluck('total')->map(fn ($v) => (int) $v)->values(),
            'dailyRevenueLabels' => $dailyRevenue->pluck('date_commande')->values(),
            'dailyRevenueData' => $dailyRevenue->pluck('ca_journalier')->map(fn ($v) => round((float) $v, 2))->values(),
        ]);
    }
}