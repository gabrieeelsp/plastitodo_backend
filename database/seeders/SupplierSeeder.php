<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('suppliers')->insert([ // 1
            'name' => "Movipack",
            'direccion' => 'Zevallos 3421',
            'telefono' => '34212344',
            'telefono_movil' => '341432211',
            'email' => 'contacto@movipack.com.ar'
        ]);

        DB::table('suppliers')->insert([ // 2
            'name' => "Ticoral",
        ]);

        DB::table('suppliers')->insert([ // 3
            'name' => "Envases Portugal",
        ]);

        DB::table('suppliers')->insert([ // 4
            'name' => "PalisticFood",
        ]);

        DB::table('suppliers')->insert([ // 5
            'name' => "Queth",
        ]);

        DB::table('suppliers')->insert([ // 6
            'name' => "Papelera Central",
        ]);

        DB::table('suppliers')->insert([ // 7
            'name' => "Tyna",
        ]);

        DB::table('suppliers')->insert([
            'name' => "Decor magic",
        ]);
        DB::table('suppliers')->insert([
            'name' => "Casa Nestor",
        ]);

        DB::table('suppliers')->insert([
            'name' => "LYL Futura",
        ]);

        DB::table('suppliers')->insert([
            'name' => "Impreplast",
        ]);

        DB::table('suppliers')->insert([
            'name' => "Mapapack",
        ]);

        DB::table('suppliers')->insert([
            'name' => "Evacor",
        ]);

        DB::table('suppliers')->insert([
            'name' => "Envadec",
        ]);

        DB::table('suppliers')->insert([
            'name' => "Suipacha",
        ]);

        DB::table('suppliers')->insert([
            'name' => "Low Cost",
        ]);

        DB::table('suppliers')->insert([
            'name' => "Polimundi",
        ]);

        DB::table('suppliers')->insert([
            'name' => "Jeraco",
        ]);

        DB::table('suppliers')->insert([
            'name' => "Dique",
        ]);
    }
}
