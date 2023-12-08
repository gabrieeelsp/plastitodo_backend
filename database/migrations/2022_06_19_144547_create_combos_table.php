<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCombosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('combos', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('desc_min', 4, 2)->default(10);
            $table->decimal('desc_may', 4, 2)->default(5);
            $table->decimal('precio_min', 10, 4)->default(0);
            $table->decimal('precio_may', 10, 4)->default(0);
            $table->boolean('is_enable')->default(false);
            $table->boolean('is_editable')->default(true);
            $table->integer('precision_min')->default(2);
            $table->integer('precision_may')->default(2);

            $table->string('image')->nullable();

            $table->boolean('is_enable_web')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('combos');
    }
}
