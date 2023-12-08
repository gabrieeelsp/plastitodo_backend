<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;

class SaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // -- 1 ---------------------------------
        DB::table('sales')->insert([
            'user_id' => 1,
            'client_id' => 1,
            'sucursal_id' => 1,
            'total' => 4000,

            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('saleitems')->insert([
            'sale_id' => 1,
            'saleproduct_id' => 2,
            'precio' => 100,
            'cantidad' => 10,
            'ivaaliquot_id' => 4           
        ]);  

        DB::table('saleitems')->insert([
            'sale_id' => 1,
            'saleproduct_id' => 5,
            'precio' => 100,
            'cantidad' => 10,
            'ivaaliquot_id' => 2           
        ]); 

        DB::table('saleitems')->insert([
            'sale_id' => 1,
            'saleproduct_id' => 5,
            'precio' => 100,
            'cantidad' => 10,
            'ivaaliquot_id' => 3          
        ]);   

        DB::table('saleitems')->insert([
            'sale_id' => 1,
            'saleproduct_id' => 5,
            'precio' => 100,
            'cantidad' => 10,
            'ivaaliquot_id' => 1          
        ]); 
        
        DB::table('payments')->insert([
            'paymentmethod_id' => 1,
            'sale_id' => 1,
            'valor' => 4000,
            'caja_id' => 1
        ]);

        DB::table('refunds')->insert([
            'paymentmethod_id' => 1,
            'sale_id' => 1,
            'valor' => 2000,
            'caja_id' => 1
        ]);


        // -- 2 ---------------------------------
        DB::table('sales')->insert([
            'user_id' => 1,
            'client_id' => 3,
            'sucursal_id' => 1,
            'total' => 40000,

            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('saleitems')->insert([
            'sale_id' => 2,
            'saleproduct_id' => 2,
            'precio' => 4000,
            'cantidad' => 10,
            'ivaaliquot_id' => 4           
        ]);

        DB::table('payments')->insert([
            'paymentmethod_id' => 2,
            'sale_id' => 2,
            'valor' => 1000,
            'caja_id' => 1
        ]);

        // -- 3 ---------------------------------
        DB::table('sales')->insert([
            'user_id' => 3,
            'client_id' => 3,
            'sucursal_id' => 1,
            'total' => 2000,

            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('saleitems')->insert([
            'sale_id' => 3,
            'saleproduct_id' => 2,
            'precio' => 100,
            'cantidad' => 10,
            'ivaaliquot_id' => 1           
        ]);
        DB::table('saleitems')->insert([
            'sale_id' => 3,
            'saleproduct_id' => 1,
            'precio' => 100,
            'cantidad' => 10,
            'ivaaliquot_id' => 3          
        ]);
    }
}
