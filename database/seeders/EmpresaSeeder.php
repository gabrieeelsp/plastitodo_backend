<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;

class EmpresaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('empresas')->insert([
            'name' => "Plastitodo",
            'cuit' => '30714071633',
            'razon_social' => 'PICCO GABRIEL SEBASTIAN PICCO IRINA NADIA Y PICCO IVANA BRENDA SH',
            'domicilio_comercial' => 'Baigorria 1306',
            'ing_brutos' => '0214032075',
            'fecha_inicio_act' => '01/05/2013',

            'ivacondition_id' => 1
        ]);
    }
}
