<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Plat, Menu, Regime, Theme, Allergene};

class CatalogueTraiteurSeeder extends Seeder
{
    public function run(): void
    {
        $plats = [
            ['titre' => 'Suprême de volaille aux morilles', 'photo' => 'images/plats/volaille_morilles.jpg', 'allergenes' => ['Lactose']],
            ['titre' => 'Saumon en croûte d’herbes', 'photo' => 'images/plats/saumon_herbes.jpg', 'allergenes' => ['Poisson']],
            ['titre' => 'Risotto aux champignons', 'photo' => 'images/plats/risotto_champignons.jpg', 'allergenes' => ['Lactose']],
            ['titre' => 'Curry de légumes au lait de coco', 'photo' => 'images/plats/curry_legumes.jpg', 'allergenes' => []],
            ['titre' => 'Magret de canard aux fruits rouges', 'photo' => 'images/plats/magret_fruits_rouges.jpg', 'allergenes' => []],
            ['titre' => 'Tajine de légumes', 'photo' => 'images/plats/tajine_legumes.jpg', 'allergenes' => []],
            ['titre' => 'Salade de chèvre chaud', 'photo' => 'images/plats/salade_chevre.jpg', 'allergenes' => ['Lactose', 'Gluten']],
            ['titre' => 'Tartare de saumon', 'photo' => 'images/plats/tartare_saumon.jpg', 'allergenes' => ['Poisson']],
            ['titre' => 'Velouté de potiron', 'photo' => 'images/plats/veloute_potiron.jpg', 'allergenes' => ['Lactose']],
            ['titre' => 'Brochettes de crevettes', 'photo' => 'images/plats/brochettes_crevettes.jpg', 'allergenes' => []],
            ['titre' => 'Fondant au chocolat', 'photo' => 'images/plats/fondant_chocolat.jpg', 'allergenes' => ['Lactose', 'Oeuf', 'Gluten']],
            ['titre' => 'Tarte aux fruits rouges', 'photo' => 'images/plats/tarte_fruits_rouges.jpg', 'allergenes' => ['Lactose', 'Oeuf', 'Gluten']],
        ];

        foreach ($plats as $p) {
            $plat = Plat::updateOrCreate(['titre_plat' => $p['titre']], ['photo' => $p['photo']]);
            $ids = Allergene::whereIn('libelle', $p['allergenes'])->pluck('id')->all();
            $plat->allergenes()->sync($ids);
        }

        $menus = [
            ['Printemps gourmand', 4, 28, 'Classique', 'Pâques', 'Une sélection printanière avec volaille, légumes et dessert fruité.', 25, [6, 1, 12]],
            ['Pâques végétariennes', 4, 26, 'Végétarien', 'Pâques', 'Cuisine végétarienne aux légumes de saison.', 22, [9, 3, 12]],
            ['Réveillon traditionnel', 6, 39, 'Classique', 'Noël', 'Un repas de fête généreux et raffiné.', 18, [2, 5, 11]],
            ['Noël végétal', 6, 32, 'Vegan', 'Noël', 'Saveurs végétales festives sans produit animal.', 18, [4, 6]],
            ['Mariage élégance', 10, 49, 'Classique', 'Mariage', 'Réception gastronomique aux saveurs délicates.', 30, [8, 1, 12]],
            ['Mariage jardin', 10, 35, 'Végétarien', 'Mariage', 'Un banquet végétarien raffiné.', 28, [7, 3, 12]],
            ['Baptême douceur', 6, 29, 'Classique', 'Baptême', 'Repas familial doux et convivial.', 26, [9, 1, 12]],
            ['Baptême sans gluten', 6, 31, 'Sans gluten', 'Baptême', 'Menu sans ingrédients contenant du gluten dans les recettes prévues.', 20, [8, 5]],
            ['Escapade méditerranéenne', 4, 30, 'Végétarien', 'Estival', 'Légumes ensoleillés et notes aromatiques.', 35, [7, 6, 12]],
            ['Été gourmand', 4, 33, 'Classique', 'Estival', 'Une table estivale légère et colorée.', 35, [8, 2, 12]],
            ['Voyage en Orient', 4, 27, 'Vegan', 'Voyage', 'Tajine et curry de légumes parfumés.', 32, [6, 4]],
            ['Voyage des saveurs', 4, 36, 'Classique', 'Voyage', 'Un assortiment de découvertes culinaires.', 24, [10, 2, 11]],
        ];

        foreach ($menus as $m) {
            $menu = Menu::updateOrCreate(
                ['titre' => $m[0]],
                ['nombre_personne_minimum' => $m[1], 'prix_par_personne' => $m[2],
                 'regime_id' => Regime::where('libelle', $m[3])->firstOrFail()->id,
                 'theme_id' => Theme::where('libelle', $m[4])->firstOrFail()->id,
                 'description' => $m[5], 'quantite_restante' => $m[6]]
            );
            $ids = collect($m[7])->map(fn ($i) => Plat::where('titre_plat', $plats[$i - 1]['titre'])->firstOrFail()->id)->all();
            $menu->plats()->sync($ids);
        }
        $this->command->info('Catalogue traiteur chargé : 12 menus et 12 plats.');
    }
}