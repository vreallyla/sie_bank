<?php

namespace Database\Seeders;

use App\Models\Produk;
use Illuminate\Database\Seeder;

class ProdukSeeder extends Seeder
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
                'nama'=>'Silver Card',
                'limit'=>4000000,
            ],
            [
                'nama'=>'Gold Card',
                'limit'=>10000000,
            ],
            [
                'nama'=>'Platinum Card',
                'limit'=>18000000,
            ],
        ];

        foreach($data as $dt){
            Produk::create($dt);
        }
    }
}
