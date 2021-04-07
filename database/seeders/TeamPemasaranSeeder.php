<?php

namespace Database\Seeders;

use App\Models\TeamPemasaran;
use App\Models\TeamPemasaranDetail;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;


class TeamPemasaranSeeder extends Seeder
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
        $admin = collect(
            User::where('level', 'admin')->get('id')
        )->pluck('id')->chunk(4)->all();
        $leader = collect(User::where('level', 'eksekutif')->get('id')
            ->skip(count($wilayah)))->pluck('id')->all();
        $anggota = [];

        foreach ($leader as $i => $dt) {
            $tim = TeamPemasaran::create([
                'team_leader_id' => $dt,
                'wilayah_id' => $wilayah[rand(0, count($wilayah)-1)]
            ]);

            foreach ($admin[$i] as $row) {
                TeamPemasaranDetail::create([
                    'team_id' => $tim->id,
                    'user_id' => $row
                ]);
            }
        }
    }
}
