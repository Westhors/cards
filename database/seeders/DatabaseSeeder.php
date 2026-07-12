<?php

namespace Database\Seeders;

use App\Models\ContactPeople;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call([
            // UsersTableSeeder::class,
            AdminSeeder::class,
            // CategorySeeder::class,
            // BrandSeeder::class,
            // CountriesTableSeeder::class,
        ]);
    }
}


