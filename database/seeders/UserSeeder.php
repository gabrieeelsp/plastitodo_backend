<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Gabriel',
            'surname' => 'Picco',
            'email' => 'test@mail.com',
            'password' => bcrypt('secret999'),

            'credito_disponible' => 1000,
            'direccion' => 'Alicia morea de justo 666',

            //'nombre_fact' => 'La chota SA',

            //'ivacondition_id' => 1,
            //'doctype_id' => 1,
            //'docnumber' => '20458967939',
            //'direccion_fact' => 'Alicia morea de justo 6348',

            'role' => 'ADMINISTRADOR',


        ]);

        /*
        DB::table('users')->insert([
            'name' => 'carolina',
            'surname' => 'Saavedra',
            'email' => 'caro@mail.com',
            'password' => bcrypt('secret999'),

        ]);

        DB::table('users')->insert([
            'name' => 'Astor',
            'surname' => 'Picco Saavedra',
            'email' => 'astor@mail.com',
            'password' => bcrypt('secret999'),

            

        ]);

        DB::table('users')->insert([
            'name' => 'Pescaditos el Surubí SA',
            'email' => 'pescadito@mail.com',
            'password' => bcrypt('secret999'),

            

            'tipo' => 'MINORISTA',

            'tipo_persona' => 'JURIDICA',
        ]);

        */
    
    }
}
