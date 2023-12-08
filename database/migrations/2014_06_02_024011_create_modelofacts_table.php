<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModelofactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modelofacts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('monto_max_no_id_efectivo')->nullable();
            $table->decimal('monto_max_no_id_no_efectivo')->nullable();

            $table->integer('id_afip_factura');
            $table->integer('id_afip_nc');
            $table->integer('id_afip_nd');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('modelofacts');
    }
}
