<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelPrecioCodigoToPurchaseproducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchaseproducts', function (Blueprint $table) {
            $table->string('rel_precio_codigo')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchaseproducts', function (Blueprint $table) {
            $table->dropColumn(['rel_precio_codigo']);
        });
    }
}
