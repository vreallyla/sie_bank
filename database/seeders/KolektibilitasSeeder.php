<?php

namespace Database\Seeders;

use App\Models\Kolektibilitas;
use Illuminate\Database\Seeder;

class KolektibilitasSeeder extends Seeder
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
                'nama' => 'Kredit Lancar',
                'singkatan' => 'L',
            ],
            [
                'nama' => 'Dalam Perhatian Khusus',
                'singkatan' => 'DPK',
            ],
            [
                'nama' => 'Kurang Lancar',
                'singkatan' => 'KL',
            ],
            [
                'nama' => 'Diragukan',
                'singkatan' => 'D',
            ],
            [
                'nama' => 'Macet',
                'singkatan' => 'M',
            ],

        ];
        foreach ($data as $dt) {
            Kolektibilitas::create($dt);
        }
    }
}
