<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('id_ID');
        $maks_eksekutif = 20;
        $z = 0;
        $kat = [
            'eksekutif',
            'admin'
        ];

        foreach (range(0, 84) as $i) {

            $email = $faker->unique()->safeEmail;
            $level = rand(0, 1);

            if($level==0 && $z<$maks_eksekutif){
                $z++;
            }else{
                $level=1;
            }

            User::create([
                'name' => $faker->name,
                'email' => $email,
                'username' => explode('@', $email)[0],
                'level' => $kat[$level],
                'email_verified_at' => now(),
                'password' => bcrypt('secret'),
            ]);
        }
    }
}
