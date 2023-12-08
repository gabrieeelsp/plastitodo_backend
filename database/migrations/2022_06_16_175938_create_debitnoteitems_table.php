<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDebitnoteitemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('debitnoteitems', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion')->nullable();
            $table->decimal('valor', 15, 4)->default(0);
            
            $table->foreignId('debitnote_id')->nullable()->constrained('debitnotes')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('ivaaliquot_id')->nullable()->constrained('ivaaliquots')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('debitnoteitems');
    }
}
