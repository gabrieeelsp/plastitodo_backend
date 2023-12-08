<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreditnoteitemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('creditnoteitems', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion')->nullable();
            $table->decimal('valor', 15, 4)->default(0);
            
            $table->foreignId('creditnote_id')->nullable()->constrained('creditnotes')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('creditnoteitems');
    }
}
