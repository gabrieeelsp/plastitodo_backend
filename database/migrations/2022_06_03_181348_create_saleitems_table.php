<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleitemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('saleitems', function (Blueprint $table) {
            $table->id();
            
            $table->decimal('precio', 10, 4)->default(0);
            $table->decimal('cantidad', 10, 4)->default(0);
            $table->decimal('cantidad_total', 10, 4)->nullable()->default(0);

            $table->foreignId('saleproduct_id')->constrained('saleproducts')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('sale_id')->constrained('sales')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('ivaaliquot_id')->constrained('ivaaliquots')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('saleitems');
    }
}
