<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Stockproductgroup;

class StockproductgroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Stockproductgroup::factory()->count(100)->create();
        DB::table('stockproductgroups')->insert([ //4
            'name' => 'Alfajor 38g GUAYMALLEN UNIDAD',
        ]);
    }
}
