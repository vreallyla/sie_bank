<?php

namespace Database\Seeders;

use App\Models\Penghasilan;
use Illuminate\Database\Seeder;

class PenghasilanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data=[
           
            [
                'nama'=>'≥4jt - <6jt',
                'min'=>4000000,
                'max'=>5999999,
            ],
            [
                'nama'=>'≥6jt - <10jt',
                'min'=>6000000,
                'max'=>9999999,
            ],
            [
                'nama'=>'≥10jt - <16jt',
                'min'=>10000000,
                'max'=>15999999,
            ],
            [
                'nama'=>'≥16jt - <23jt',
                'min'=>16000000,
                'max'=>22999999,
            ],
            [
                'nama'=>'≥23jt - <30jt',
                'min'=>23000000,
                'max'=>29999999,
            ],
            [
                'nama'=>'≥30jt',
                'min'=>30000000,
                'max'=>null,
            ],
        ];

        foreach($data as $dt){
            Penghasilan::create($dt);
        }
    }
}
