<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Seeder;

class IvaconditionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //  --- 1 -----------------------------------
        DB::table('ivaconditions')->insert([
            'id_afip' => 1,
            'name' => 'IVA Responsable Inscripto',
            'modelofact_id' => 1
        ]);
/* 
        DB::table('ivacondition_modelofact')->insert([
            'ivacondition_id' => 1,
            'modelofact_id' => 1
        ]);
        DB::table('ivacondition_modelofact')->insert([
            'ivacondition_id' => 1,
            'modelofact_id' => 2
        ]); */

        //  --- 2 -----------------------------------
        DB::table('ivaconditions')->insert([
            'id_afip' => 4,
            'name' => 'IVA Sujeto Exento',
            'modelofact_id' => 2
        ]);
/*         DB::table('ivacondition_modelofact')->insert([
            'ivacondition_id' => 2,
            'modelofact_id' => 2
        ]); */

        //  --- 3 -----------------------------------
        DB::table('ivaconditions')->insert([
            'id_afip' => 5,
            'name' => 'Consumidor Final',
            'modelofact_id' => 2
        ]);
/*         DB::table('ivacondition_modelofact')->insert([
            'ivacondition_id' => 3,
            'modelofact_id' => 2
        ]); */

        //  --- 4 -----------------------------------
        DB::table('ivaconditions')->insert([
            'id_afip' => 13,
            'name' => 'Monotributista Social',
            'modelofact_id' => 2
        ]);

/*         DB::table('ivacondition_modelofact')->insert([
            'ivacondition_id' => 4,
            'modelofact_id' => 2
        ]); */
        //  --- 5 -----------------------------------
        DB::table('ivaconditions')->insert([
            'id_afip' => 6,
            'name' => 'Responsable Monotributo',
            'modelofact_id' => 1
        ]);

/*         DB::table('ivacondition_modelofact')->insert([
            'ivacondition_id' => 4,
            'modelofact_id' => 2
        ]);

        DB::table('ivacondition_modelofact')->insert([
            'ivacondition_id' => 1,
            'modelofact_id' => 1
        ]);
        DB::table('ivacondition_modelofact')->insert([
            'ivacondition_id' => 1,
            'modelofact_id' => 2
        ]); */
    }
}
