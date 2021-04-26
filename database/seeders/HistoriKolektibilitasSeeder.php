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
        $nasabah = Nasabah::selectRaw('id, kolektibilitas_id, month(tgl_real) bulan,year(tgl_real) tahun')
        ->get();
        $admin=collect(User::where('level','admin')->get(['id']))->pluck('id')->all();

        foreach ($nasabah as $dt) {

            foreach (range($dt->tahun, now()->format('Y')) as $tahun) {
                foreach (range($dt->tahun==now()->format('Y')?$dt->bulan:1, ($tahun == now()->format('Y') ? now()->format('m') : 12)) as $bulan) {
                    HistoriKolektibilitas::create([
                        'nasabah_id' => $dt->id,
                        'kolektibilitas_id'=>
                        $tahun == now()->format('Y') && $bulan==now()->format('m') ?$dt->kolektibilitas_id:
                        rand(1,(count($kolektibilitas))),
                        'tgl_pembaruan' => $tahun . '-' . $bulan. '-' . rand(1, 5) ,
                        'admin_id'=>$admin[rand(0,(count($admin)-1))],
                    ]);
                }
            }
           
        }
    }
}
