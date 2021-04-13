<?php

namespace Database\Seeders;

use App\Models\Kolektibilitas;
use App\Models\Nasabah;
use App\Models\Pekerjaan;
use App\Models\Pendidikan;
use App\Models\Penghasilan;
use App\Models\Produk;
use App\Models\TeamPemasaran;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class NasabahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('id_ID');
        $wilayah = collect(Wilayah::all('id'))->pluck('id')->all();
        $pekerjaan = collect(Pekerjaan::all('id'))->pluck('id')->all();
        $penghasilan = collect(Penghasilan::all('id'))->pluck('id')->all();
        $pendidikan = collect(Pendidikan::all('id'))->pluck('id')->all();
        $kolektibilitas = collect(Kolektibilitas::all('id'))->pluck('id')->all();
        $produk = collect(Produk::all('id'))->pluck('id')->all();
        $leader = collect(
            TeamPemasaran::all()
        )->groupBy('wilayah_id')->all();
        $product_code = ['SC', 'GC', 'PC'];

        foreach (range(0, 1000) as $dt) {
            $tahun = rand(2018, now()->format('Y'));
            $tanggal = rand(1, 28);
            $bulan = rand(1, $tahun==now()->format('Y')?now()->format('m'):12);
            
            $pick_produk = rand(1, (count($produk)));
            $kode_debitur = $product_code[$pick_produk - 1] . substr($tahun, 2) . str_pad($bulan, 2, '0', STR_PAD_LEFT);
            $wilayah_id=rand(1, (count($wilayah)));

            $urutan = Nasabah::where('no_debitur', 'like', "%$kode_debitur%")->get()->count() + 1;

            Nasabah::create([
                'nik' => $faker->unique()->nik(),
                'tgl_real' => "$tahun-$bulan-$tanggal",
                'no_debitur' => $kode_debitur . str_pad($urutan, 5, '0', STR_PAD_LEFT),
                'nama' => $faker->name(),
                'tgl_lahir' => $faker->date('Y-m-d'),
                'alamat_anggunan' => $faker->streetAddress,
                'alamat_instansi' => $faker->streetAddress,
                'wilayah_id' => $wilayah_id,
                'pekerjaan_id' => rand(1, (count($pekerjaan))),
                'penghasilan_id' => rand(1, (count($penghasilan))),
                'pendidikan_id' => rand(1, (count($pendidikan))),
                'kolektibilitas_id' => rand(1, (count($kolektibilitas))),
                'produk_id' => $pick_produk,
                'team_id' => $leader[$wilayah_id][rand(0,count($leader[$wilayah_id])-1)]->id,
                'telp' => $faker->PhoneNumber,
            ]);
        }
    }
}
