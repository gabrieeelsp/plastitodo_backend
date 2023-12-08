<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModelofactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('modelofacts')->insert([
            'name' => "A",
            'monto_max_no_id_efectivo' => 0,
            'monto_max_no_id_no_efectivo' => 0,

            'id_afip_factura' => 1,
            'id_afip_nc' => 3,
            'id_afip_nd' => 2,
        ]);

        DB::table('modelofacts')->insert([
            'name' => "B",
            'monto_max_no_id_efectivo' => 17000,
            'monto_max_no_id_no_efectivo' => 31000,

            'id_afip_factura' => 6,
            'id_afip_nc' => 8,
            'id_afip_nd' => 7,
        ]);
    }
}
