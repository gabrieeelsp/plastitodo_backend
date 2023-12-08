<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSucursalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sucursals', function (Blueprint $table) {
            $table->id();
            
            $table->string('name');
            $table->string('direccion')->nullable();
            $table->string('telefono')->nullable();
            $table->string('telefono_movil')->nullable();

            $table->string('horario')->nullable();
            
            $table->integer('punto_venta_fe')->nullable();

            $table->foreignId('empresa_id')->nullable()->constrained('empresas')->onUpdate('cascade')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sucursals');
    }
}
