<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class WilayahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('id_ID');
        $eks = collect(User::where('level', 'eksekutif')->get(['id']))->pluck('id')->all();
        $wilayah = [
            'Surabaya Timur',
            'Surabaya Selatan',
            'Surabaya Barat',
            'Surabaya Utara',
        ];

        foreach ($wilayah as $i => $dt) {


            Wilayah::create([
                'nama' => $dt,
                'kacap_id' => $eks[$i],
                'telp' => $faker->phoneNumber,
                'alamat' => $faker->unique()->address,
            ]);
        }
    }
}
