<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;

class UtilisateursSeeder extends Seeder
{
    public function run(): void
    {
        // Vérification des rôles existants
        $roleClient = Role::where('libelle', 'client')->firstOrFail();
        $roleEmploye = Role::where('libelle', 'employe')->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | Employés
        |--------------------------------------------------------------------------
        */

        $employes = [
            ['Julie', 'Martin', '0612345601', 'Bordeaux', '33000', '12 rue Sainte-Catherine'],
            ['José', 'Bernard', '0612345602', 'Bordeaux', '33000', '25 rue Judaïque'],
            ['Camille', 'Petit', '0612345603', 'Mérignac', '33700', '8 avenue de la Marne'],
            ['Thomas', 'Dubois', '0612345604', 'Pessac', '33600', '15 rue des Écoles'],
            ['Sophie', 'Laurent', '0612345605', 'Talence', '33400', '6 rue Victor Hugo'],
        ];

        /*
        |--------------------------------------------------------------------------
        | Clients
        |--------------------------------------------------------------------------
        */

        $clients = [
            ['Emma', 'Lefebvre', '0623456701', 'Bordeaux', '33000', '10 rue du Palais Gallien'],
            ['Lucas', 'Michel', '0623456702', 'Mérignac', '33700', '18 avenue de Verdun'],
            ['Chloé', 'Garcia', '0623456703', 'Pessac', '33600', '5 rue des Lilas'],
            ['Hugo', 'Roux', '0623456704', 'Talence', '33400', '22 rue de la République'],
            ['Léa', 'Fontaine', '0623456705', 'Bègles', '33130', '14 rue du Prêche'],
            ['Nathan', 'Chevalier', '0623456706', 'Cenon', '33150', '9 avenue Jean Jaurès'],
            ['Manon', 'François', '0623456707', 'Lormont', '33310', '11 rue du Général de Gaulle'],
            ['Louis', 'Legrand', '0623456708', 'Gradignan', '33170', '4 rue des Acacias'],
            ['Inès', 'Gauthier', '0623456709', 'Bordeaux', '33000', '31 rue Fondaudège'],
            ['Gabriel', 'Garnier', '0623456710', 'Eysines', '33320', '7 avenue du Médoc'],
            ['Jade', 'Faure', '0623456711', 'Le Bouscat', '33110', '16 rue Pasteur'],
            ['Arthur', 'Rousseau', '0623456712', 'Bruges', '33520', '3 rue des Érables'],
            ['Alice', 'Vincent', '0623456713', 'Floirac', '33270', '20 avenue Pasteur'],
            ['Raphaël', 'Muller', '0623456714', 'Blanquefort', '33290', '8 rue de la Gare'],
            ['Louise', 'Lambert', '0623456715', 'Bordeaux', '33800', '27 cours de la Marne'],
        ];

        /*
        |--------------------------------------------------------------------------
        | Création des comptes
        |--------------------------------------------------------------------------
        */

        $creerUtilisateur = function (array $donnees, int $roleId, string $prefixe) {

            [$prenom, $nom, $telephone, $ville, $codePostal, $adresse] = $donnees;

            $email = strtolower($prefixe . '.' .
                iconv('UTF-8', 'ASCII//TRANSLIT', $prenom) . '.' .
                iconv('UTF-8', 'ASCII//TRANSLIT', $nom) .
                '@example.com');

            User::firstOrCreate(
                ['email' => $email],
                [
                    'prenom' => $prenom,
                    'nom' => $nom,
                    'telephone' => $telephone,
                    'ville' => $ville,
                    'code_postal' => $codePostal,
                    'pays' => 'France',
                    'adresse_postale' => $adresse,
                    'role_id' => $roleId,
                    'password' => Hash::make('Demo2026!Test'),
                    'is_active' => true,
                ]
            );
        };

        foreach ($employes as $employe) {
            $creerUtilisateur($employe, $roleEmploye->id, 'employe');
        }

        foreach ($clients as $client) {
            $creerUtilisateur($client, $roleClient->id, 'client');
        }

        $this->command->info('20 utilisateurs fictifs créés ou déjà présents.');
    }
}
