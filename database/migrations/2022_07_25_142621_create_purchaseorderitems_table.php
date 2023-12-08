<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseorderitemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchaseorderitems', function (Blueprint $table) {
            $table->id();

            $table->decimal('cantidad', 10, 4)->default(0);

            $table->foreignId('purchaseproduct_id')->nullable()->constrained('purchaseproducts')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('purchaseorder_id')->nullable()->constrained('purchaseorders')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchaseorderitems');
    }
}
