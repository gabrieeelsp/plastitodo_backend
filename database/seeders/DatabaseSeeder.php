<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Saleproduct;
use App\Models\Stockproduct;
use App\Models\Sucursal;
use App\Models\User;

use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(ValorSeeder::class);
        $this->call(DeliveryshiftSeeder::class);
        //$this->call(TagSeeder::class);
        //$this->call(FamiliaSeeder::class);
        $this->call(ModelofactSeeder::class);
        $this->call(IvaconditionSeeder::class);
        $this->call(EmpresaSeeder::class);
        $this->call(SucursalSeeder::class);

        $this->call(PaymentmethodSeeder::class);

        
        $this->call(IvaaliquotSeeder::class);
        $this->call(DoctypeSeeder::class);
        


        $this->call(UserSeeder::class);

        //$this->call(CajaSeeder::class);

        //$this->call(SaleproductgroupSeeder::class);

        //$this->call(StockproductgroupSeeder::class);
        //$this->call(SupplierSeeder::class);
        //$this->call(StockproductSeeder::class);
        //$this->call(ComboSeeder::class);

        

        //$this->call(SaleSeeder::class);


        /* foreach ( Stockproduct::all() as $stockproduct ) {
            foreach( Sucursal::all() as $sucursal ) {
                DB::table('stocksucursals')->insert([
                    'stockproduct_id' => $stockproduct->id,
                    'sucursal_id' => $sucursal->id,
                    'stock' => rand(0, 1000),
                    'stock_minimo' => rand(0, 200),
                    'stock_maximo' => rand(200, 800),
                ]);
            }
        } 
    

        foreach ( Saleproduct::all() as $saleproduct ) {
            $saleproduct->set_precios($saleproduct->stockproduct->costo);
            $saleproduct->save();
        }

        
        $astor = User::find(3);
        $astor->tags()->attach(1);
        $astor->save(); */
    }
}
