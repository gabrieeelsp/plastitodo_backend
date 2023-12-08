<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComprobantesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comprobantes', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->nullable();
            $table->string('punto_venta');

            $table->string('cae')->nullable();
            $table->string('cae_fch_vto')->nullable();

            $table->integer('id_afip_tipo');

            $table->integer('comprobanteable_id');
            $table->string('comprobanteable_type');

            $table->string('docnumber');
            $table->string('doctype_id_afip');
            $table->string('doctype_name');

            $table->string('nombre_empresa');
            $table->string('domicilio_comercial_empresa');
            $table->string('cuit_empresa');
            $table->string('ing_brutos_empresa');
            $table->string('fecha_inicio_act_empresa');
            $table->string('razon_social_empresa');
            $table->string('ivacondition_name_empresa');
            
            $table->string('condicion_venta');
            $table->string('nombre_fact_client')->nullable();
            $table->string('direccion_fact_client')->nullable();
            $table->string('ivacondition_name_client')->nullable();

            $table->timestamps();

            $table->foreignId('modelofact_id')->constrained('modelofacts')->onUpdate('cascade')->onDelete('cascade');
            //$table->foreignId('doctype_id')->constrained('doctypes')->onUpdate('cascade')->onDelete('cascade');
            //$table->foreignId('ivacondition_id')->constrained('ivaconditions')->onUpdate('cascade')->onDelte('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comprobantes');
    }
}
