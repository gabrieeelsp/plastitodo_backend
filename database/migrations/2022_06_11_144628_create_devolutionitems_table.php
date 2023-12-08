<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDevolutionitemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('devolutionitems', function (Blueprint $table) {
            $table->id();
            $table->decimal('cantidad', 10, 4)->default(0);
            $table->decimal('cantidad_total', 10, 4)->nullable()->default(0);

            $table->foreignId('saleitem_id')->constrained('saleitems')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('devolution_id')->constrained('devolutions')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('devolutionitems');
    }
}
