```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use App\Models\{
    User,
    Role,
    Regime,
    Allergene,
    Plat,
    Menu,
    Commande,
    Theme
};

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Rôles
        |--------------------------------------------------------------------------
        */

        $adminRole = Role::updateOrCreate(
            ['id' => 1],
            ['libelle' => 'admin']
        );

        $clientRole = Role::updateOrCreate(
            ['id' => 2],
            ['libelle' => 'client']
        );

        $employeRole = Role::updateOrCreate(
            ['id' => 3],
            ['libelle' => 'employe']
        );

        /*
        |--------------------------------------------------------------------------
        | Administrateur
        |--------------------------------------------------------------------------
        |
        | Aucun utilisateur fictif n'est généré avec une Factory.
        | On crée uniquement le compte administrateur nécessaire.
        |
        */

        User::updateOrCreate(
            ['email' => 'admin@test.com'],
            [
                'password' => Hash::make('Password123!'),
                'prenom' => 'Admin',
                'nom' => 'Super',
                'telephone' => null,
                'ville' => null,
                'code_postal' => null,
                'pays' => 'France',
                'adresse_postale' => null,
                'role_id' => $adminRole->id,
                'is_active' => true,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Régimes
        |--------------------------------------------------------------------------
        */

        $regimes = [
            'Classique',
            'Végétarien',
            'Vegan',
            'Sans gluten',
        ];

        foreach ($regimes as $libelle) {
            Regime::firstOrCreate([
                'libelle' => $libelle,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Thèmes
        |--------------------------------------------------------------------------
        */

        $themes = [
            'Pâques',
            'Noël',
            'Baptême',
            'Mariage',
        ];

        foreach ($themes as $libelle) {
            Theme::firstOrCreate([
                'libelle' => $libelle,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Allergènes
        |--------------------------------------------------------------------------
        */

        $allergenes = [
            'Gluten',
            'Lactose',
            'Arachide',
            'Oeuf',
            'Poisson',
        ];

        foreach ($allergenes as $libelle) {
            Allergene::firstOrCreate([
                'libelle' => $libelle,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Données métier
        |--------------------------------------------------------------------------
        |
        | Les anciennes lignes utilisant :
        |
        | Plat::factory(...)
        | Menu::factory(...)
        | Commande::factory(...)
        |
        | ont volontairement été supprimées.
        |
        | Les factories dépendent généralement de Faker, qui n'est pas installé
        | dans l'environnement de production Heroku.
        |
        | Les plats, menus et commandes seront créés depuis l'application ou
        | pourront être ajoutés plus tard avec des données réelles.
        |
        */

        $this->command->info('Base de données initialisée avec succès.');
        $this->command->info('Rôles, administrateur, régimes, thèmes et allergènes créés.');
    }
}
```