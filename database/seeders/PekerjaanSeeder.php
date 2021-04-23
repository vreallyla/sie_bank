<?php

namespace Database\Seeders;

use App\Models\Pekerjaan;
use Illuminate\Database\Seeder;

class PekerjaanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $kategori = [

            // 'Administrator',
            // 'Aktor',
            // 'Akuntan',
            // 'Antariksawan',
            // 'Apoteker',
            // 'Arsitek',
            // 'Atlet',

            // 'Bartender',
            // 'Bidan',
            // 'Blogger',
            // 'Buruh',

            // 'Calo',
            // 'Camat',

            // 'Desainer',
            // 'Direktur',
            // 'Dokter',
            // 'Dokter hewan',
            // 'Dosen',

            // 'Editor',

            // 'Fotografer',

            // 'Gamer',
            // 'Guide',
            // 'Guru',

            // 'Ilmuwan',
            // 'Ilustrator',
            // 'Insinyur',
            // 'Inspektur',
            // 'Instruktur',

            // 'Jaksa',
            // 'Jurnalis',

            // 'Karyawan',
            // 'Kasir',
            // 'Kiai',
            // 'Koki',
            // 'Komikus',
            // 'Kondektur',
            // 'Konsultan',

            // 'Lurah',

            // 'Manajer',
            // 'Marketing',
            // 'Masinis',
            // 'Model',
            // 'Montir',

            // 'Nakhoda ',
            // 'Nelayan',
            // 'Novelis',
            // 'Notaris',

            // 'Operator',
            // 'Olahragawan',

            // 'Pastor',
            // 'Pedagang',
            // 'Pegawai Negeri Sipil',
            // 'Pekerja sosial',
            // 'Pelaut',
            // 'Pelayan',
            // 'Pelukis',
            // 'Pemadam kebakaran',
            // 'Pemahat',            
            // 'Programmer',
            // 'Penari',
            // 'Pendeta',
            // 'Peneliti',
            // 'Penerjemah',
            // 'Pengacara',
            // 'Pengantar surat',
            // 'Penulis',
            // 'Penyanyi',
            // 'Desainer',
            // 'Perawat',
            // 'Peretas',
            // 'Perminyakan',
            // 'Petani',
            // 'Peternak',
            // 'Polisi',
            // 'Politikus',
            // 'Pramugari',
            // 'Programmer',
            // 'Psikiater',
            // 'Psikolog',
            // 'Pilot',
            // 'Pramusaji',
            // 'Pramugara',
            // 'Presiden',

            // 'Raja',
            // 'Ratu',
            // 'Refractionis Optisen',
            // 'Resepsionis',

            
            // 'Satpam',
            // 'Sekretaris',
            // 'Selebriti',
            // 'Seniman',
            // 'Sopir',

            // 'Petani',
            // 'Tengkulak',
            // 'TNI',
            // 'Tukang',

            // 'Ustad',

            // 'Video editor',

            // 'Wartawan',
            // 'Wirausahawan',

            // 'Youtuber',

            'PNS',
            'Tentara',
            'Karyawan',
            'Wirausahawan',
            'Seniman',
            'Pemuka Agama',
            'Bidang Hukum',
            'Olahragawan'
        ];

        foreach($kategori as $dt){
            Pekerjaan::create(['nama'=>$dt]);
        }
    }
}
