<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;

use App\Models\Sucursal;
use App\Models\Stockproduct;
use App\Models\Saleproduct;
use App\Models\Purchaseproduct;

class StockproductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        //Stockproduct::factory()->count(1000)->create();

        //Saleproduct::factory()->count(1500)->create();

        //Purchaseproduct::factory()->count(1300)->create();

        
        //--1---------------------------------------------
        DB::table('stockproducts')->insert([
            'name' => 'Alfajor 38g Blanco GUAYMALLEN UNIDAD',
            'ivaaliquot_id' => 4,
            'costo' => 12.5,

            'stockproductgroup_id' => 1,

            'familia_id' => 2,
        ]);
        
        DB::table('stocksucursals')->insert([
            'stockproduct_id' => 1,
            'sucursal_id' => 1,
            'stock' => 100,
            'stock_minimo' => 80,
            'stock_maximo' => 200,
        ]);
        DB::table('stocksucursals')->insert([
            'stockproduct_id' => 1,
            'sucursal_id' => 2,
            'stock' => 30,
            'stock_minimo' => 80,
            'stock_maximo' => 200,
        ]);
        DB::table('saleproducts')->insert([ // 1
            'stockproduct_id' => 1,
            'name' => 'Alfajor 38g Blanco GUAYMALLEN UNIDAD',
            'relacion_venta_stock' => 1,
            'porc_min' => 40,
            'porc_may' => 30,

            'is_enable' => true,

            'precision_min' => 0,
            'precision_may' => 1,

            'saleproductgroup_id' => 1,
        ]);
        DB::table('saleproducts')->insert([ // 2
            'stockproduct_id' => 1,
            'name' => 'Alfajor 38g Blanco GUAYMALLEN Caja x40',
            'relacion_venta_stock' => 40,
            'porc_min' => 20,
            'porc_may' => 15,

            'is_enable' => true,

            'precision_min' => -1,
            'precision_may' => 1,

            'saleproductgroup_id' => 2,

            'desc_min' => 10,
            'desc_may' => 5,

            'fecha_desc_desde' => new Carbon('2022-08-08'),
            'fecha_desc_hasta' => new Carbon('2022-09-10 23:59'),
        ]);

        DB::table('purchaseproducts')->insert([ // 1
            'stockproduct_id' => 1,
            'name' => 'Alfajor 38g Blanco GUAYMALLEN Caja x40',
            'relacion_compra_stock' => 40,

            'supplier_id' => 7,
        ]);

        //--2---------------------------------------------
        DB::table('stockproducts')->insert([
            'name' => 'Alfajor 38g Negro GUAYMALLEN UNIDAD',
            'ivaaliquot_id' => 4,
            'costo' => 12.5,

            'stockproductgroup_id' => 1,
            'familia_id' => 2,
        ]);
        
        DB::table('stocksucursals')->insert([
            'stockproduct_id' => 2,
            'sucursal_id' => 2,
            'stock' => 20,
            'stock_minimo' => 80,
            'stock_maximo' => 200,
        ]);
        DB::table('stocksucursals')->insert([
            'stockproduct_id' => 2,
            'sucursal_id' => 1,
            'stock' => 200,
            'stock_minimo' => 80,
            'stock_maximo' => 200,
        ]); 
        DB::table('saleproducts')->insert([ // 3
            'stockproduct_id' => 2,
            'name' => 'Alfajor 38g Negro GUAYMALLEN UNIDAD',
            'relacion_venta_stock' => 1,
            'porc_min' => 40,
            'porc_may' => 30,

            'is_enable' => true,

            'precision_min' => 0,
            'precision_may' => 1,

            'saleproductgroup_id' => 1,
            
        ]);
        DB::table('saleproducts')->insert([ //4
            'stockproduct_id' => 2,
            'name' => 'Alfajor 38g Negro GUAYMALLEN Caja x40',
            'relacion_venta_stock' => 40,
            'porc_min' => 20,
            'porc_may' => 15,

            'is_enable' => true,

            'precision_min' => -1,
            'precision_may' => 1,

            'saleproductgroup_id' => 2,

            'desc_min' => 10,
            'desc_may' => 0,

            'fecha_desc_desde' => new Carbon('2022-07-08'),
            'fecha_desc_hasta' => new Carbon('2022-09-10 23:59'),
        ]);

        DB::table('purchaseproducts')->insert([ // 1
            'stockproduct_id' => 2,
            'name' => 'Alfajor 38g Negro GUAYMALLEN Caja x40',
            'relacion_compra_stock' => 40,

            'supplier_id' => 7,
        ]);

        //--3---------------------------------------------
        DB::table('stockproducts')->insert([
            'name' => 'Chupetin Pelotitas LHERITIER PAQx50',
            'ivaaliquot_id' => 4,
            'costo' => 220,

            'familia_id' => 2,
        ]);
        
        DB::table('stocksucursals')->insert([
            'stockproduct_id' => 3,
            'sucursal_id' => 1,
            'stock' => 0,
            'stock_minimo' => 5,
            'stock_maximo' => 12,
        ]);
         DB::table('stocksucursals')->insert([
            'stockproduct_id' => 3,
            'sucursal_id' => 2,
            'stock' => 25,
            'stock_minimo' => 5,
            'stock_maximo' => 12,
        ]);
        
        DB::table('saleproducts')->insert([ // 5
            'stockproduct_id' => 3,
            'name' => 'Chupetin Pelotitas LHERITIER PAQx50',
            'relacion_venta_stock' => 1,
            'porc_min' => 40,
            'porc_may' => 15,

            'is_enable' => true,

            'precision_min' => -1,
            'precision_may' => 1,
            
        ]);

        DB::table('purchaseproducts')->insert([ // 1
            'stockproduct_id' => 3,
            'name' => 'Chupetin Pelotitas LHERITIER Caja x12PAQ',
            'relacion_compra_stock' => 12,

            'supplier_id' => 7,
        ]);

        //--4---------------------------------------------
        DB::table('stockproducts')->insert([
            'name' => 'Heladitos Paisandu PAQx30',
            'ivaaliquot_id' => 4,
            'costo' => 130
        ]);
        
        DB::table('stocksucursals')->insert([
            'stockproduct_id' => 4,
            'sucursal_id' => 2,
            'stock' => 5,
            'stock_minimo' => 0,
            'stock_maximo' => 30,
        ]);
        DB::table('stocksucursals')->insert([
            'stockproduct_id' => 4,
            'sucursal_id' => 1,
            'stock' => 10,
            'stock_minimo' => 10,
            'stock_maximo' => 40,
        ]);
        
        DB::table('saleproducts')->insert([ // 6
            'stockproduct_id' => 4,
            'name' => 'Heladitos Paisandu PAQx30',
            'relacion_venta_stock' => 1,
            'porc_min' => 40,
            'porc_may' => 15,

            'is_enable' => true,

            'precision_min' => -1,
            'precision_may' => 1,
        ]);

        DB::table('purchaseproducts')->insert([ // 1
            'stockproduct_id' => 4,
            'name' => 'Heladitos Paisandu Caja x30PAQ',
            'relacion_compra_stock' => 30,

            'supplier_id' => 7,
        ]);

        //--5---------------------------------------------
        DB::table('stockproducts')->insert([
            'name' => 'Harina de Trigo PAGx1Kg',
            'ivaaliquot_id' => 3,
            'costo' => 90,

            'familia_id' => 2,
        ]);
        
        DB::table('stocksucursals')->insert([
            'stockproduct_id' => 5,
            'sucursal_id' => 1,
            'stock' => 20,
            'stock_minimo' => 10,
            'stock_maximo' => 50,
        ]);
        DB::table('stocksucursals')->insert([
            'stockproduct_id' => 5,
            'sucursal_id' => 2,
            'stock' => 35,
            'stock_minimo' => 10,
            'stock_maximo' => 50,
        ]);

        DB::table('saleproducts')->insert([ // 6
            'stockproduct_id' => 5,
            'name' => 'Harina de Trigo PAGx1Kg',
            'relacion_venta_stock' => 1,
            'porc_min' => 40,
            'porc_may' => 15,

            'is_enable' => true,

            'precision_min' => -1,
            'precision_may' => 2,

            'desc_min' => 10,
            'desc_may' => 5,

            'fecha_desc_desde' => new Carbon('2022-07-08'),
            'fecha_desc_hasta' => new Carbon('2022-07-10 23:59'),

        ]);

        DB::table('purchaseproducts')->insert([ // 1
            'stockproduct_id' => 5,
            'name' => 'Harina de Trigo BULTO x20Kg',
            'relacion_compra_stock' => 20,

            'supplier_id' => 1,
        ]);


        //--6---------------------------------------------
        DB::table('stockproducts')->insert([
            'name' => 'Bandeja 103 Micro UNIDAD',
            'ivaaliquot_id' => 4,
            'costo' => 5.5
        ]);

        DB::table('stocksucursals')->insert([
            'stockproduct_id' => 6,
            'sucursal_id' => 1,
            'stock' => 3500,
            'stock_minimo' => 2000,
            'stock_maximo' => 6000,
        ]);
        DB::table('stocksucursals')->insert([
            'stockproduct_id' => 6,
            'sucursal_id' => 2,
            'stock' => 700,
            'stock_minimo' => 2000,
            'stock_maximo' => 6000,
        ]);

        DB::table('saleproducts')->insert([ // 3
            'stockproduct_id' => 6,
            'name' => 'Bandeja 103 Micro UNIDAD',
            'relacion_venta_stock' => 1,
            'porc_min' => 40,
            'porc_may' => 30,

            'is_enable' => true,

            'precision_min' => 1,
            'precision_may' => 1,
        ]);
        DB::table('saleproducts')->insert([ //4
            'stockproduct_id' => 6,
            'name' => 'Bandeja 103 Micro PAQx100',
            'relacion_venta_stock' => 100,
            'porc_min' => 25,
            'porc_may' => 15,

            'is_enable' => true,

            'precision_min' => -1,
            'precision_may' => 1,
        ]);

        DB::table('purchaseproducts')->insert([ // 1
            'stockproduct_id' => 6,
            'name' => 'Bandeja 103 Micro AZUL Bulto x 6PAQ',
            'relacion_compra_stock' => 600,

            'supplier_id' => 1,
        ]);

        //--7---------------------------------------------
        DB::table('stockproducts')->insert([
            'name' => 'Dulce de Leche EUREKA 10Kg UNIDAD',
            'ivaaliquot_id' => 4,
            'costo' => 2800,

            'familia_id' => 2,
        ]);

        DB::table('stocksucursals')->insert([
            'stockproduct_id' => 7,
            'sucursal_id' => 2,
            'stock' => 13,
            'stock_minimo' => 5,
            'stock_maximo' => 20,
        ]);
        DB::table('stocksucursals')->insert([
            'stockproduct_id' => 7,
            'sucursal_id' => 1,
            'stock' => 2,
            'stock_minimo' => 15,
            'stock_maximo' => 30,
        ]); 

        DB::table('saleproducts')->insert([ // 3
            'stockproduct_id' => 7,
            'name' => 'Dulce de Leche EUREKA 10Kg UNIDAD',
            'relacion_venta_stock' => 1,
            'porc_min' => 40,
            'porc_may' => 15,

            'is_enable' => true,

            'precision_min' => -1,
            'precision_may' => 0,
        ]);
        DB::table('saleproducts')->insert([ //4
            'stockproduct_id' => 7,
            'name' => 'Dulce de Leche EUREKA xKILO',
            'relacion_venta_stock' => 0.1,
            'porc_min' => 40,
            'porc_may' => 30,

            'is_enable' => true,

            'precision_min' => -1,
            'precision_may' => -1,
        ]);

        DB::table('purchaseproducts')->insert([ // 1
            'stockproduct_id' => 7,
            'name' => 'Dulce de Leche EUREKA 10Kg',
            'relacion_compra_stock' => 1,

            'supplier_id' => 7,
        ]);

        //--8---------------------------------------------
        DB::table('stockproducts')->insert([
            'name' => 'Tapita de Alfajor FANTOCHE CAJAx3,50Kg',
            'ivaaliquot_id' => 4,
            'costo' => 850,
            'familia_id' => 2,
        ]);

        DB::table('stocksucursals')->insert([
            'stockproduct_id' => 8,
            'sucursal_id' => 1,
            'stock' => 5,
            'stock_minimo' => 5,
            'stock_maximo' => 10,
        ]);
        DB::table('stocksucursals')->insert([
            'stockproduct_id' => 8,
            'sucursal_id' => 2,
            'stock' => 1,
            'stock_minimo' => 5,
            'stock_maximo' => 10,
        ]); 
        
        DB::table('saleproducts')->insert([ // 3
            'stockproduct_id' => 8,
            'name' => 'Tapita de Alfajor FANTOCHE CAJAx3,50Kg',
            'relacion_venta_stock' => 1,
            'porc_min' => 40,
            'porc_may' => 15,

            'is_enable' => true,

            'precision_min' => -1,
            'precision_may' => 1,
        ]);
        DB::table('saleproducts')->insert([ //4
            'stockproduct_id' => 8,
            'name' => 'Tapita de Alfajor FANTOCHE xKILO',
            'relacion_venta_stock' => 0.28571,
            'porc_min' => 40,
            'porc_may' => 30,

            'is_enable' => true,

            'precision_min' => -1,
            'precision_may' => 0,

        ]);

        DB::table('purchaseproducts')->insert([ // 1
            'stockproduct_id' => 8,
            'name' => 'Tapita de Alfajor FANTOCHE Caja x3,5Kg',
            'relacion_compra_stock' => 1,

            'supplier_id' => 7,
        ]);

        //--9---------------------------------------------
        DB::table('stockproducts')->insert([
            'name' => 'Grana Azul DECORMAGIC PAQx1Kg',
            'ivaaliquot_id' => 4,
            'costo' => 240,
            'familia_id' => 2,
        ]);

        DB::table('stocksucursals')->insert([
            'stockproduct_id' => 9,
            'sucursal_id' => 1,
            'stock' => 10,
            'stock_minimo' => 2,
            'stock_maximo' => 5,
        ]);
        DB::table('stocksucursals')->insert([
            'stockproduct_id' => 9,
            'sucursal_id' => 2,
            'stock' => 3,
            'stock_minimo' => 2,
            'stock_maximo' => 5,
        ]);
        
        DB::table('saleproducts')->insert([ // 3
            'stockproduct_id' => 9,
            'name' => 'Grana Azul DECORMAGIC PAQx1Kg',
            'relacion_venta_stock' => 1,
            'porc_min' => 40,
            'porc_may' => 15,

            'is_enable' => true,

            'precision_min' => -1,
            'precision_may' => 1,
        ]);
        DB::table('saleproducts')->insert([ //4
            'stockproduct_id' => 9,
            'name' => 'Grana Azul DECORMAGIC 100g',
            'relacion_venta_stock' => 0.1,
            'porc_min' => 40,
            'porc_may' => 30,

            'precision_min' => 0,
            'precision_may' => 0,
        ]);

        DB::table('purchaseproducts')->insert([ // 1
            'stockproduct_id' => 9,
            'name' => 'Grana Azul DECORMAGIC Bulto x 10Kg',
            'relacion_compra_stock' => 10,

            'supplier_id' => 7,
        ]);

        //--10---------------------------------------------
        DB::table('stockproducts')->insert([
            'name' => 'Papel Prensa en rollo 40cm UNIDAD',
            'ivaaliquot_id' => 4,
            'costo' => 210,

            'familia_id' => 2,

            'is_stock_unitario_variable' => true,
            'stock_aproximado_unidad' => 6,
        ]);

        DB::table('stocksucursals')->insert([
            'stockproduct_id' => 10,
            'sucursal_id' => 1,
            'stock' => 5,
            'stock_minimo' => 5,
            'stock_maximo' => 10,
            
        ]);
        DB::table('stocksucursals')->insert([
            'stockproduct_id' => 10,
            'sucursal_id' => 2,
            'stock' => 9,
            'stock_minimo' => 10,
            'stock_maximo' => 20,
        ]);
        
        DB::table('saleproducts')->insert([ // 3
            'stockproduct_id' => 10,
            'name' => 'Papel Prensa en rollo 40cm UNIDAD',
            'relacion_venta_stock' => 1,
            'porc_min' => 40,
            'porc_may' => 15,

            'is_enable' => true,

            'precision_min' => 0,
            'precision_may' => 1,
        ]);

        DB::table('purchaseproducts')->insert([ // 1
            'stockproduct_id' => 10,
            'name' => 'Papel Prensa en rollo 40cm UNIDAD',
            'relacion_compra_stock' => 1,

            'supplier_id' => 1,
        ]);

        //--11---------------------------------------------
        DB::table('stockproducts')->insert([
            'name' => 'Caramelos RICOMAS PAQx100',
            'ivaaliquot_id' => 4,
            'costo' => 170
        ]);

        DB::table('stocksucursals')->insert([
            'stockproduct_id' => 11,
            'sucursal_id' => 1,
            'stock' => 100,
            'stock_minimo' => 10,
            'stock_maximo' => 30,
        ]);
        DB::table('stocksucursals')->insert([
            'stockproduct_id' => 11,
            'sucursal_id' => 2,
            'stock' => 30,
            'stock_minimo' => 10,
            'stock_maximo' => 30,
        ]);
        
        DB::table('saleproducts')->insert([ // 3
            'stockproduct_id' => 11,
            'name' => 'Caramelos RICOMAS PAQx100',
            'relacion_venta_stock' => 1,
            'porc_min' => 40,
            'porc_may' => 15,

            'is_enable' => true,

            'precision_min' => -1,
            'precision_may' => 1,
        ]);

        DB::table('purchaseproducts')->insert([ // 1
            'stockproduct_id' => 11,
            'name' => 'Caramelos RICOMAS Caja x30PAQ',
            'relacion_compra_stock' => 20,

            'supplier_id' => 7,
        ]);
    }
}
