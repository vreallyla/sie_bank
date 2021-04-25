<?php

namespace Database\Seeders;

use App\Models\Pendidikan;
use Illuminate\Database\Seeder;

class PendidikanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'nama' => 'Sekolah Dasar',
                'singkatan' => 'SD',
            ],
            [
                'nama' => 'Sekolah Menengah Pertama',
                'singkatan' => 'SMP',
            ],
            [
                'nama' => 'Sekolah Menengah Atas',
                'singkatan' => 'SMA',
            ],
            [
                'nama' => 'Sekolah Menengah Kejuruan',
                'singkatan' => 'SMK',
            ],
            [
                'nama' => 'Ahli Madya',
                'singkatan' => 'D3',
            ],
            [
                'nama' => 'Sarjana',
                'singkatan' => 'S1/D4',
            ],
            [
                'nama' => 'Magister',
                'singkatan' => 'S2',
            ],
            [
                'nama' => 'Doktor',
                'singkatan' => 'S3',
            ],
        ];

        foreach($data as $dt){
            Pendidikan::create($dt);
        }
    }
}
