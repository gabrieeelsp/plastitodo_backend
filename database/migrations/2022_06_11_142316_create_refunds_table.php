<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRefundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->decimal('valor', 10, 4)->default(0);
            $table->decimal('saldo', 15, 4)->default(0);
            $table->boolean('is_confirmed')->default(false);
            $table->timestamps();

            $table->foreignId('paymentmethod_id')->constrained('paymentmethods')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('sale_id')->constrained('sales')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('caja_id')->constrained('cajas')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('refunds');
    }
}
