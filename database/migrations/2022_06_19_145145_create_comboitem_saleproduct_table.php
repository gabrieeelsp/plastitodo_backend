<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComboitemSaleproductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comboitem_saleproduct', function (Blueprint $table) {
            $table->id();
            $table->integer('comboitem_id')->unsigned();
            $table->integer('saleproduct_id')->unsigned();
            $table->boolean('is_enable')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comboitem_saleproduct');
    }
}
