<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            RoleSeeder::class,
            RegionSeeder::class,
            DepartmentSeeder::class,
            UserSeeder::class,
            CategorySeeder::class,
            PlaceSeeder::class,
            ReviewSeeder::class,
            FavoriteSeeder::class,
            ImageSeeder::class,
            NotificationSeeder::class
        ]);
    }
}
