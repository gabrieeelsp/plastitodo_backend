<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDevolutioncombosaleproductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('devolutioncombosaleproducts', function (Blueprint $table) {
            $table->id();
            $table->decimal('cantidad', 10, 4)->default(0);
            
            $table->foreignId('devolutioncomboitem_id')->constrained('devolutioncomboitems')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('salecombosaleproduct_id')->constrained('salecombosaleproducts')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('devolutioncombosaleproducts');
    }
}
