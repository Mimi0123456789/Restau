<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;

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

        $grafanaBaseUrl = config('services.grafana.url');
        $grafanaDashboardUid = config('services.grafana.dashboard_uid');

        $query = [];

        if ($dateDebut) {
            $query['from'] = strtotime($dateDebut . ' 00:00:00') * 1000;
        }

        if ($dateFin) {
            $query['to'] = strtotime($dateFin . ' 23:59:59') * 1000;
        }

        if ($menuId) {
            $query['var-menu_id'] = $menuId;
        }

        $grafanaUrl = null;

        if ($grafanaBaseUrl && $grafanaDashboardUid) {
            $grafanaUrl = rtrim($grafanaBaseUrl, '/')
                . '/d/' . $grafanaDashboardUid . '/comptabilite-statistiques'
                . '?orgId=1&kiosk=tv';

            if (!empty($query)) {
                $grafanaUrl .= '&' . http_build_query($query);
            }
        }

        return view('dashboard.admin.statistiques', compact(
            'menus',
            'menuId',
            'dateDebut',
            'dateFin',
            'grafanaUrl'
        ));
    }
}