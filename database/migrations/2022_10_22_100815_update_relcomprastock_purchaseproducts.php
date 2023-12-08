<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\DB;

class UpdateRelcomprastockPurchaseproducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    DB::select(DB::raw('ALTER TABLE purchaseproducts CHANGE COLUMN relacion_compra_stock relacion_compra_stock decimal(10, 4) DEFAULT 1;'));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::select(DB::raw('ALTER TABLE purchaseproducts CHANGE COLUMN relacion_compra_stock relacion_compra_stock decimal(7, 4) DEFAULT 1;'));
    }
}
