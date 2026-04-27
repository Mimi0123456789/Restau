<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{
    User, Role, Regime, Allergene, Plat, Menu, Commande, Theme
};

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Roles
        |--------------------------------------------------------------------------
        */
        $adminRole = Role::create(['libelle' => 'admin']);
        $clientRole = Role::create(['libelle' => 'client']);
        $employeRole = Role::create(['libelle' => 'employe']);

        $adminRoleId = $adminRole->getKey();
        $clientRoleId = $clientRole->getKey();
        $employeRoleId = $employeRole->getKey();

        /*
        |--------------------------------------------------------------------------
        | Users
        |--------------------------------------------------------------------------
        */
        User::factory(10)->create([
            'role_id' => $clientRoleId,
        ]);

        User::factory(5)->create([
            'role_id' => $employeRoleId,
        ]);

        User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'password' => bcrypt('password'),
                'prenom' => 'Admin',
                'nom' => 'Super',
                'telephone' => null,
                'ville' => null,
                'code_postal' => null,
                'pays' => 'France',
                'adresse_postale' => null,
                'role_id' => $adminRoleId,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Régimes
        |--------------------------------------------------------------------------
        */
        foreach (['Classique', 'Végétarien', 'Vegan', 'Sans gluten'] as $libelle) {
            Regime::create(['libelle' => $libelle]);
        }

        /*
        |--------------------------------------------------------------------------
        | Thème
        |--------------------------------------------------------------------------
        */
        foreach (['Pâques', 'Noël', 'Baptême', 'Mariage'] as $libelle) {
            Theme::create(['libelle' => $libelle]);
        }

        /*
        |--------------------------------------------------------------------------
        | Allergènes
        |--------------------------------------------------------------------------
        */
        foreach (['Gluten', 'Lactose', 'Arachide', 'Oeuf', 'Poisson'] as $libelle) {
            Allergene::create(['libelle' => $libelle]);
        }

        /*
        |--------------------------------------------------------------------------
        | Plats
        |--------------------------------------------------------------------------
        */
        $plats = Plat::factory(15)->create();

        /*
        |--------------------------------------------------------------------------
        | Menus
        |--------------------------------------------------------------------------
        */
        $regimeIds = Regime::pluck('id')->toArray();
        $themeIds = Theme::pluck('id')->toArray();

        $menus = Menu::factory(6)->create([
            'regime_id' => collect($regimeIds)->random(),
            'theme_id' => collect($themeIds)->random(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Relations menus ↔ plats
        |--------------------------------------------------------------------------
        */
        foreach ($menus as $menu) {
            $menu->plats()->attach(
                $plats->random(rand(2, 5))->pluck('id')->toArray()
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Relations plats ↔ allergènes
        |--------------------------------------------------------------------------
        */
        $allergenesIds = Allergene::pluck('id')->toArray();

        foreach ($plats as $plat) {
            $plat->allergenes()->attach(
                collect($allergenesIds)->random(rand(0, 3))
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Commandes
        |--------------------------------------------------------------------------
        */
        $userIds = User::pluck('id')->toArray();

        Commande::factory(20)->create([
            'user_id' => collect($userIds)->random(),
        ]);
    }
}
