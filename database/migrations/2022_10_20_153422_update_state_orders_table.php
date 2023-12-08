<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateStateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	DB::statement("ALTER TABLE orders CHANGE state state ENUM('EDITANDO', 'FINALIZADO', 'CONFIRMADO', 'EN PREPARACION', 'PREPARADO', 'FACTURADO', 'EN DISTRIBUCION', 'ENTREGADO', 'CANCELADO')");    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
	DB::statement("ALTER TABLE orders CHANGE state state ENUM('EDITANDO', 'FINALIZADO', 'CONFIRMADO', 'EN PREPARACION', 'PREPARADO', 'FACTURADO', 'EN DISTRIBUCION', 'ENTREGADO')");    
    }
}
