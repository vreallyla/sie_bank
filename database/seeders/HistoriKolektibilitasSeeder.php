<?php

namespace Database\Seeders;

use App\Models\HistoriKolektibilitas;
use App\Models\Kolektibilitas;
use App\Models\Nasabah;
use App\Models\User;
use Illuminate\Database\Seeder;

class HistoriKolektibilitasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $kolektibilitas = collect(Kolektibilitas::all('id'))->pluck('id')->all();
        $nasabah = Nasabah::all();
        $admin=collect(User::where('level','admin')->get(['id']))->pluck('id')->all();

        foreach ($nasabah as $dt) {

            foreach (range(2018, 2021) as $tahun) {
                foreach (range(1, ($tahun == 2021 ? 3 : 12)) as $bulan) {
                    HistoriKolektibilitas::create([
                        'nasabah_id' => $dt->id,
                        'kolektibilitas_id'=>
                        $tahun == 2021 && $bulan==3 ?$dt->kolektibilitas_id:
                        rand(1,(count($kolektibilitas))),
                        'tgl_pembaruan' => $tahun . '-' . $bulan. '-' . rand(1, 5) ,
                        'admin_id'=>$admin[rand(0,(count($admin)-1))],
                    ]);
                }
            }
           
        }
    }
}
