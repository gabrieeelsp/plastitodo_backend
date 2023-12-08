<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockmovementitemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stockmovementitems', function (Blueprint $table) {
            $table->id();

            $table->decimal('cantidad', 10, 4)->default(0);

            $table->foreignId('stockproduct_id')->nullable()->constrained('stockproducts')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('stockmovement_id')->nullable()->constrained('stockmovements')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stockmovementitems');
    }
}
