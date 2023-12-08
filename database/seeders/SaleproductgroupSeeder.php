<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;

use App\Models\Saleproductgroup;

class SaleproductgroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Saleproductgroup::factory()->count(100)->create();

        DB::table('saleproductgroups')->insert([ //4
            'name' => 'Alfajor 38g GUAYMALLEN UNIDAD',
        ]);

        DB::table('saleproductgroups')->insert([ //4
            'name' => 'Alfajor 38g GUAYMALLEN CAJA x40',
        ]);
    }
}
