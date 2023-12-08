<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCajasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cajas', function (Blueprint $table) {
            $table->id();
            $table->decimal('dinero_inicial', 15, 4)->default(0);
            $table->decimal('dinero_final', 15, 4)->nullable();
            $table->boolean('is_open')->default(true);

            $table->timestampTz('close_at')->nullable();
            $table->timestamps();

            $table->foreignId('user_id')->nullable()->constrained('users')->nullable()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('sucursal_id')->nullable()->constrained('sucursals')->nullable()->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cajas');
    }
}
