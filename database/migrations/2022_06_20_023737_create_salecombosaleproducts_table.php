<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalecombosaleproductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salecombosaleproducts', function (Blueprint $table) {
            $table->id();
            $table->decimal('cantidad', 10, 4)->default(0);
            
            $table->foreignId('salecomboitem_id')->constrained('salecomboitems')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('saleproduct_id')->constrained('saleproducts')->onUpdate('cascade')->onDelte('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('salecombosaleproducts');
    }
}
