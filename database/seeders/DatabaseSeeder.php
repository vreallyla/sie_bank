<?php

namespace Database\Seeders;

use App\Models\HistoriKolektibilitas;
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
        // \App\Models\User::factory(10)->create();
        $this->call([
            PendidikanSeeder::class,
            PekerjaanSeeder::class,
            PenghasilanSeeder::class,
            KolektibilitasSeeder::class,
            UserSeeder::class,
            WilayahSeeder::class,
            ProdukSeeder::class,
            TeamPemasaranSeeder::class,
            NasabahSeeder::class,
            HistoriKolektibilitasSeeder::class,

            
           
        ]);
    }
}
