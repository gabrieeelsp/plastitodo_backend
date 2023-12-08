<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockmovementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stockmovements', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->enum('tipo', ['EGRESO', 'INGRESO'])->defatul('INGRESO');
            $table->enum('estado', ['PENDIENTE', 'CONFIRMADO'])->defatul('PENDIENTE');

            $table->string('comments', 200)->nullable();

            $table->foreignId('user_id')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('sucursal_id')->nullable()->constrained('sucursals')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stockmovements');
    }
}
