<?php

namespace Database\Seeders;

use App\Models\Image;
use Illuminate\Database\Seeder;

class ImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // images des catégories

        for ($i = 1; $i < 10; $i++) {
            Image::create([
                'name' => 'categorie' . $i . '.jpg',
                'cover' => true,
                'user_id' => 1,
                'place_id' => null,
                'status' => 'validée'
            ]);
        }

        // images des placex

        Image::create([
            'name' => 'mervent.jpg',
            'cover' => true,
            'user_id' => 1,
            'place_id' => 1,
            'status' => 'validée'
        ]);

        Image::create([
            'name' => 'parc-des-oiseaux-du-marais.jpg',
            'cover' => true,
            'user_id' => 1,
            'place_id' => 2,
            'status' => 'validée'
        ]);

        Image::create([
            'name' => 'jardin-des-plantes-nantes.jpg',
            'cover' => true,
            'user_id' => 1,
            'place_id' => 3,
            'status' => 'validée'
        ]);

        Image::create([
            'name' => 'zoo-de-la-palmyre.jpg',
            'cover' => true,
            'user_id' => 1,
            'place_id' => 4,
            'status' => 'validée'
        ]);

        Image::create([
            'name' => 'planete-sauvage.jpg',
            'cover' => true,
            'user_id' => 1,
            'place_id' => 5,
            'status' => 'validée'
        ]);

        Image::create([
            'name' => 'marais-poitevin.jpg',
            'cover' => true,
            'user_id' => 1,
            'place_id' => 6,
            'status' => 'validée'
        ]);

        Image::create([
            'name' => 'plage-des-chardons.jpg',
            'cover' => true,
            'user_id' => 1,
            'place_id' => 7,
            'status' => 'validée'
        ]);

        Image::create([
            'name' => 'plage-des-conches.jpg',
            'cover' => true,
            'user_id' => 1,
            'place_id' => 8,                
            'status' => 'validée'
        ]);

        Image::create([
            'name' => 'parc-pre-leroy.jpg',
            'cover' => true,
            'user_id' => 1,
            'place_id' => 9,
            'status' => 'validée'
        ]);

        Image::create([
            'name' => 'vallee-des-singes.jpg',
            'cover' => true,
            'user_id' => 1,
            'place_id' => 10,
            'status' => 'validée'
        ]);

        // images pour les placex aléatoires

        for ($i = 11; $i < 111; $i++) {

            Image::create([
                'name' => 'defaultpicture.jpg',
                'cover' => true,
                'user_id' => 1,
                'place_id' => $i,
                'status' => 'validée'
            ]);
        }

        // image pour tests notifications

        Image::create([
            'name' => 'imagetest1.jpg',
            'cover' => true,
            'user_id' => 2,
            'place_id' => 100,
            'status' => 'en attente'
        ]);

        Image::create([
            'name' => 'imagetest2.jpg',
            'cover' => true,
            'user_id' => 2,
            'place_id' => 100,
            'status' => 'en attente'
        ]);

        Image::create([
            'name' => 'imagetest3.jpg',
            'cover' => true,
            'user_id' => 2,
            'place_id' => 100,
            'status' => 'en attente'
        ]);
    }
}
