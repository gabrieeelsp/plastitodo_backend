<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStocktransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stocktransfers', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->enum('estado', ['EDITANDO', 'FINALIZADO', 'EN PREPARACION', 'PREPARADO', 'EN DISTRIBUCION', 'INGRESANDO', 'RECIBIDO'])->defatul('EDITANDO');

            $table->boolean('is_recibido')->default(false);

            $table->timestampTz('recibido_at')->nullable();

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
        Schema::dropIfExists('stocktransfers');
    }
}
