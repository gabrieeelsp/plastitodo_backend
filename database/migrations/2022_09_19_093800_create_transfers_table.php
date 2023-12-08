<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->enum('estado_origen', ['PENDIENTE', 'CONFIRMADO'])->defatul('PENDIENTE');
            $table->enum('estado_destino', ['PENDIENTE', 'CONFIRMADO'])->defatul('PENDIENTE');

            $table->foreignId('user_origen_id')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('user_destino_id')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('sucursal_origen_id')->nullable()->constrained('sucursals')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('sucursal_destino_id')->nullable()->constrained('sucursals')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transfers');
    }
}
