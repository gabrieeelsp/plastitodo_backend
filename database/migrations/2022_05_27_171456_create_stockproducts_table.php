<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockproductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stockproducts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('costo', 15, 4)->default(0);

            $table->boolean('is_stock_unitario_variable')->default(false);
            $table->decimal('stock_aproximado_unidad', 15, 4)->default(0);

            $table->timestampTz('time_set_costo')->nullable();

            $table->string('image')->nullable();
            $table->boolean('is_enable')->default(true);

            $table->foreignId('ivaaliquot_id')->nullable()->constrained('ivaaliquots')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('stockproductgroup_id')->nullable()->constrained('stockproductgroups')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('familia_id')->nullable()->constrained('familias')->onUpdate('cascade')->onDelete('cascade');

        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stockproducts');
    }
}
