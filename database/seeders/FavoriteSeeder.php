<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Place;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FavoriteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 0; $i < 20; $i++) {
            DB::table('favorites')->insert([
                'user_id' => rand(1, User::count()),
                'place_id' => rand(1, Place::count())
            ]);
        }
    }
}
