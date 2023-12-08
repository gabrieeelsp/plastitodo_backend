<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdercombosaleproductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ordercombosaleproducts', function (Blueprint $table) {
            $table->id();
            $table->decimal('cantidad', 10, 4)->default(0);
            $table->boolean('is_prepared')->default(false);

            $table->foreignId('ordercomboitem_id')->constrained('ordercomboitems')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('saleproduct_id')->constrained('saleproducts')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ordercombosaleproducts');
    }
}
