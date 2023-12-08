<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleproductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('saleproducts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('relacion_venta_stock', 12, 4)->default(1);
            $table->boolean('is_enable')->default(false);

            $table->decimal('porc_min', 6, 2)->default(35);
            $table->decimal('precio_min', 15, 4)->default(0);
            $table->integer('precision_min')->default(2);

            $table->decimal('porc_may', 6, 2)->default(15);
            $table->decimal('precio_may', 15, 4)->default(0);
            $table->integer('precision_may')->default(2);

            $table->decimal('desc_min', 6, 2)->nullable();
            $table->decimal('precio_min_desc', 15, 4)->default(0);
            $table->decimal('desc_may', 6, 2)->nullable();
            $table->decimal('precio_may_desc', 15, 4)->default(0);

            $table->timestamp('fecha_desc_desde')->nullable();
            $table->timestamp('fecha_desc_hasta')->nullable();

            $table->string('barcode')->nullable();

            $table->string('image1')->nullable();

            $table->string('image2')->nullable();

            $table->string('image3')->nullable();

            $table->boolean('is_enable_web')->default(false);

            $table->string('comments', 200)->nullable();
            

            $table->foreignId('stockproduct_id')->nullable()->constrained('stockproducts')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('saleproductgroup_id')->nullable()->constrained('saleproductgroups')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('saleproducts');
    }
}
