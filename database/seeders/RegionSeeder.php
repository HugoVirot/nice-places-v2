<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Region;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Region::create([
            'name' => 'Auvergne-Rhône-Alpes',
        ]);

        Region::create([
            'name' => 'Bourgogne-Franche-Comté',
        ]);

        Region::create([
            'name' => 'Bretagne',
        ]);

        Region::create([
            'name' => 'Centre-Val de Loire',
        ]);

        Region::create([
            'name' => 'Corse',
        ]);

        Region::create([
            'name' => 'Grand Est',
        ]);

        Region::create([
            'name' => 'Hauts-de-France',
        ]);

        Region::create([
            'name' => 'Ile-de-France',
        ]);

        Region::create([
            'name' => 'Normandie',
        ]);

        Region::create([
            'name' => 'Nouvelle-Aquitaine',
        ]);

        Region::create([
            'name' => 'Occitanie',
        ]);

        Region::create([
            'name' => 'Pays de la Loire',
        ]);

        Region::create([
            'name' => 'Provence-Alpes-Côte d’Azur',
        ]);

        Region::create([
            'name' => 'Départements d\'Outre-Mer',
        ]);
    }
}
