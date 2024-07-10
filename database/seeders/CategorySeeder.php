<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Category::create([  //1
            'name' => 'plages',
            'icon' => '<i class="fa-solid fa-umbrella-beach"></i>',
            'color' => '#e6c875'
        ]);

        Category::create([  //2
            'name' => 'forêts et bois',
            'icon' => '<i class="fa-solid fa-tree"></i>',
            'color' => '#11ad0e'
        ]);

        Category::create([  //3
            'name' => 'lacs et étangs',
            'icon' => '<i class="fa-solid fa-droplet"></i>',
            'color' => '#0e9aad'
        ]);

        Category::create([  //4
            'name' => 'fleuves, rivières et cours d\'eau',
            'icon' => '<i class="fa-solid fa-water"></i>',
            'color' => '#0e56ad'
        ]);

        Category::create([  //5
            'name' => 'parcs naturels régionaux',
            'icon' => '<i class="fa-solid fa-signs-post"></i>',
            'color' => '#733d0a'
        ]);

        Category::create([  //6
            'name' => 'parcs urbains',
            'icon' => '<i class="fa-solid fa-tree-city"></i>',
            'color' => '#6fde2a'
        ]);

        Category::create([  //7
            'name' => 'randonnées en montagne',
            'icon' => '<i class="fa-solid fa-mountain-sun"></i>',
            'color' => '#98b5b5'
        ]);

        Category::create([  //8
            'name' => 'zoos et réserves naturelles',
            'icon' => '<i class="fa-solid fa-paw"></i>',
            'color' => '#e39d1b'
        ]);

        Category::create([  //9
            'name' => 'parcs de loisirs et accrobranches',
            'icon' => '<i class="fa-solid fa-child-reaching"></i>',
            'color' => '#1be3a4'
        ]);
    }
}
