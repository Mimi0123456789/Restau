<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Models\Menu;
use App\Models\Theme;
use App\Models\Regime;
use App\Models\Avis;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PanierController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Admin\EmployeController;
use App\Http\Controllers\Admin\ParametresController;
use App\Http\Controllers\Admin\AvisController;

use App\Http\Controllers\MenuController;
use App\Http\Controllers\PlatController;
use App\Http\Controllers\CommandeController;

use App\Http\Controllers\Client\CommandeClientController;
use App\Http\Controllers\Client\AvisClientController;

use App\Http\Controllers\Dashboard\AdminDashboardController;
use App\Http\Controllers\Dashboard\EmployeDashboardController;
use App\Http\Controllers\Dashboard\ClientDashboardController;

/*
|--------------------------------------------------------------------------
| Page d'accueil
|--------------------------------------------------------------------------
*/
Route::get('/', function (Request $request) {
    $query = Menu::with(['theme', 'regime', 'plats'])
        ->where('quantite_restante', '>', 0);

    if ($request->filled('theme_id')) {
        $query->where('theme_id', $request->theme_id);
    }

    if ($request->filled('regime_id')) {
        $query->where('regime_id', $request->regime_id);
    }

    if ($request->filled('prix_max')) {
        $query->where('prix_par_personne', '<=', $request->prix_max);
    }

    $menus = $query->orderBy('prix_par_personne')->get();
    $themes = Theme::orderBy('libelle')->get();
    $regimes = Regime::orderBy('libelle')->get();

    $avis = Avis::with('commande.user')
        ->where('statut', 'valide')
        ->latest()
        ->take(20)
        ->get();

    return view('home', compact('menus', 'themes', 'regimes', 'avis'));
})->name('home');

Route::get('/contact', [ContactController::class, 'create'])->name('contact.create');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

/*
|--------------------------------------------------------------------------
| Routes protégées (utilisateur connecté)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Route pivot dashboard (utilisée par Breeze)
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', function () {
        $user = auth()->user();

        return match ($user->role_id) {
            1 => redirect()->route('admin.dashboard'),
            2 => redirect()->route('dashboard.client'),
            3 => redirect()->route('dashboard.employe'),
            default => redirect()->route('home'),
        };
    })->name('dashboard');

/*
|--------------------------------------------------------------------------
| ADMIN + EMPLOYÉ
|--------------------------------------------------------------------------
*/
Route::middleware(['role:admin,employe'])->group(function () {

    Route::get('/dashboard/employe', [EmployeDashboardController::class, 'index'])
        ->name('dashboard.employe');

    /*
    |--------------------------------------------------------------------------
    | PARAMÈTRES : accès page + horaires
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin/parametres')
        ->name('admin.parametres.')
        ->group(function () {

            Route::get('/', [ParametresController::class, 'index'])
                ->name('index');

            // HORAIRES
            Route::post('/horaires', [ParametresController::class, 'storeHoraire'])
                ->name('horaires.store');

            Route::put('/horaires/{horaire}', [ParametresController::class, 'updateHoraire'])
                ->name('horaires.update');

            Route::delete('/horaires/{horaire}', [ParametresController::class, 'destroyHoraire'])
                ->name('horaires.destroy');
        });

    // MENUS
    Route::resource('menus', MenuController::class)
        ->only(['index', 'store', 'update', 'destroy']);

    // PLATS
    Route::resource('plats', PlatController::class)
        ->only(['store', 'update', 'destroy']);

    // COMMANDES
    Route::prefix('commandes')
        ->name('commandes.')
        ->group(function () {
            Route::get('/', [CommandeController::class, 'index'])->name('index');
            Route::get('/{commande}', [CommandeController::class, 'show'])->name('show');
            Route::patch('/{commande}/statut', [CommandeController::class, 'updateStatut'])->name('statut');
        });

    // AVIS
    Route::get('/avis', [AvisController::class, 'index'])
        ->name('avis.index');

    Route::put('/avis/{avi}', [AvisController::class, 'update'])
        ->name('avis.update');
});


    /*
    |--------------------------------------------------------------------------
    | ADMIN
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin'])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            Route::get('/dashboard', [AdminDashboardController::class, 'index'])
                ->name('dashboard');

            Route::get('/employes', [EmployeController::class, 'index'])
                ->name('employes.index');

            Route::post('/employes', [EmployeController::class, 'store'])
                ->name('employes.store');

            Route::put('/employes/{employe}', [EmployeController::class, 'update'])
                ->name('employes.update');

            Route::patch('/employes/{employe}/toggle', [EmployeController::class, 'toggle'])
                ->name('employes.toggle');

            Route::get('/statistiques', [AdminDashboardController::class, 'statistiques'])
                ->name('statistiques');
                
            Route::prefix('parametres')
                ->name('parametres.')
                ->group(function () {

                    // ALLERGÈNES
                    Route::post('/allergenes', [ParametresController::class, 'storeAllergene'])
                        ->name('allergenes.store');

                    Route::put('/allergenes/{allergene}', [ParametresController::class, 'updateAllergene'])
                        ->name('allergenes.update');

                    Route::delete('/allergenes/{allergene}', [ParametresController::class, 'destroyAllergene'])
                        ->name('allergenes.destroy');

                    // RÉGIMES
                    Route::post('/regimes', [ParametresController::class, 'storeRegime'])
                        ->name('regimes.store');

                    Route::put('/regimes/{regime}', [ParametresController::class, 'updateRegime'])
                        ->name('regimes.update');

                    Route::delete('/regimes/{regime}', [ParametresController::class, 'destroyRegime'])
                        ->name('regimes.destroy');

                    // THÈMES
                    Route::post('/themes', [ParametresController::class, 'storeTheme'])
                        ->name('themes.store');

                    Route::put('/themes/{theme}', [ParametresController::class, 'updateTheme'])
                        ->name('themes.update');

                    Route::delete('/themes/{theme}', [ParametresController::class, 'destroyTheme'])
                        ->name('themes.destroy');
                });
        });


    /*
    |--------------------------------------------------------------------------
    | CLIENT
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:client'])->group(function () {

        Route::get('/dashboard/client', [ClientDashboardController::class, 'index'])
            ->name('dashboard.client');

        Route::get('/panier', [PanierController::class, 'index'])
            ->name('panier.index');

        Route::post('/panier/ajouter/{menu}', [PanierController::class, 'add'])
            ->name('panier.add');

        Route::patch('/panier/{menu}', [PanierController::class, 'update'])
            ->name('panier.update');

        Route::delete('/panier/{menu}', [PanierController::class, 'remove'])
            ->name('panier.remove');

        Route::get('/panier/validation', [PanierController::class, 'checkoutForm'])
            ->name('panier.checkout.form');

        Route::post('/panier/valider', [PanierController::class, 'checkout'])
            ->name('panier.checkout');

        Route::get('/mon-compte/commandes', [CommandeClientController::class, 'index'])
            ->name('client.commandes.index');

        Route::get('/mon-compte/commandes/{commande}', [CommandeClientController::class, 'show'])
            ->name('client.commandes.show');

        Route::post('/mon-compte/commandes/{commande}/avis', [AvisClientController::class, 'store'])
            ->name('client.commandes.avis.store');

        Route::get('/mon-compte/commandes/{commande}/modifier', [CommandeClientController::class, 'edit'])
            ->name('client.commandes.edit');

        Route::put('/mon-compte/commandes/{commande}', [CommandeClientController::class, 'update'])
            ->name('client.commandes.update');

        Route::patch('/mon-compte/commandes/{commande}/annuler', [CommandeClientController::class, 'cancel'])
            ->name('client.commandes.cancel');
    });

    /*
    |--------------------------------------------------------------------------
    | Profil utilisateur
    |--------------------------------------------------------------------------
    */
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Auth Breeze
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';