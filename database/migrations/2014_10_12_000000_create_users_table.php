<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('surname')->nullable(); //solo si es una persona fisica, sino no lo muestro

            $table->string('email')->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();

            //$table->tinyInteger('role')->default(1);
            $table->enum('role', ['ADMINISTRADOR','RESPONSABLE SUCURSAL', 'VENDEDOR'])->nullable();

            $table->enum('tipo', ['MINORISTA','MAYORISTA'])->default('MINORISTA');

            $table->enum('tipo_persona', ['FISICA','JURIDICA'])->default('FISICA'); // lo uso para definir si se va a guardar apellido

            //--- Client -------
            $table->string('nombre_fact')->nullable(); //puede ser por ejemplo una escuela, que factura a nombre del ministerio
            $table->string('direccion_fact')->nullable();
            $table->boolean('is_fact_default')->default(false); // true -> se factura la condicion iva que tenga asignada
            $table->string('docnumber')->nullable();

            $table->string('direccion')->nullable(); // se una por ejemplo como direccion de entrega
            $table->string('coments_direccion_client', 100)->nullable();
            $table->string('telefono')->nullable(); 
            

            $table->decimal('saldo', 15, 4)->default(0);
            $table->decimal('credito_disponible', 15, 4)->default(0);

            $table->foreignId('ivacondition_id')->default(3)->constrained('ivaconditions')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('doctype_id')->default(4)->constrained('doctypes')->onUpdate('cascade')->onDelete('cascade');

            $table->string('coments_client', 200)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
