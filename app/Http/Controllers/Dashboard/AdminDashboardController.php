<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
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
            ->orderByDesc('total_commandes')
            ->get();

        return view('dashboard.admin.statistiques', [
            'menus' => $menus,
            'menuId' => $menuId,
            'dateDebut' => $dateDebut,
            'dateFin' => $dateFin,

            'statsMenus' => $statsMenus,

            'totalCommandes' => $statsMenus->sum('total_commandes'),
            'totalQuantite' => $statsMenus->sum('total_quantite'),
            'totalChiffreAffaires' => $statsMenus->sum('chiffre_affaires'),

            'commandesParMenuLabels' => $statsMenus->pluck('titre')->values(),
            'commandesParMenuData' => $statsMenus->pluck('total_commandes')->map(fn ($v) => (int) $v)->values(),

            'quantitesParMenuLabels' => $statsMenus->pluck('titre')->values(),
            'quantitesParMenuData' => $statsMenus->pluck('total_quantite')->map(fn ($v) => (int) $v)->values(),

            'caParMenuLabels' => $statsMenus->pluck('titre')->values(),
            'caParMenuData' => $statsMenus->pluck('chiffre_affaires')->map(fn ($v) => round((float) $v, 2))->values(),
        ]);
    }
}