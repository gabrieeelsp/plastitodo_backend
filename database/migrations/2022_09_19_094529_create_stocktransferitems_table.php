<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStocktransferitemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stocktransferitems', function (Blueprint $table) {
            $table->id();

            $table->decimal('cantidad', 10, 4)->default(0);

            $table->boolean('is_recibido')->default(false);
            $table->boolean('is_prepared')->default(false);

            $table->foreignId('stockproduct_id')->nullable()->constrained('stockproducts')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('stocktransfer_id')->nullable()->constrained('stocktransfers')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stocktransferitems');
    }
}
