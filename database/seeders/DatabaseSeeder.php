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
            ContinentSeeder::class,
            ContinentsTableSeeder::class,
            CountrySeeder::class,
            CountriesTableSeeder::class,
            ServiceCategoriesAndAttributesSeeder::class,
            ServiceSeeder::class,
            DestinationSeeder::class,
            TravelTypesTableSeeder::class,
            SubscriptionSeeder::class,
            TripsTableSeeder::class,
        ]);
    }
}