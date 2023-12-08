<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDevolutioncomboitemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('devolutioncomboitems', function (Blueprint $table) {
            $table->id();
            $table->decimal('cantidad', 10, 4)->default(0);
            $table->timestamps();

            $table->foreignId('devolution_id')->constrained('devolutions')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('salecomboitem_id')->constrained('salecomboitems')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('devolutioncomboitems');
    }
}
