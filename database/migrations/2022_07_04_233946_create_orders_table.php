<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->Enum('state', ['EDITANDO', 'FINALIZADO', 'CONFIRMADO', 'EN PREPARACION', 'PREPARADO', 'FACTURADO', 'EN DISTRIBUCION', 'ENTREGADO'])->default('EDITANDO');
            $table->boolean('is_delivery')->default(true);
            
            $table->timestamps();

            $table->dateTimeTz('fecha_entrega_acordada')->nullable();

            $table->integer('cant_bultos')->nullable();

            $table->foreignId('user_id')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('cascade');

            $table->foreignId('client_id')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('sucursal_id')->nullable()->constrained('sucursals')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('sale_id')->nullable()->constrained('sales')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('ivacondition_id')->nullable()->constrained('ivaconditions')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('deliveryshift_id')->nullable()->constrained('deliveryshifts')->onUpdate('cascade')->onDelete('cascade');

            //INICIADO -> el pedido fue recibido
            //AUTORIZADO -> fue revisado y se paso a armado, si el pedido es cargado por un vendedor entonces esta autorizado
            //  a partir de que es autorizado el sistema ya descuenta el stock
            //PREPARADO -> ya se armo el pedido y se eliminaron los productos que no habia stock, se cargo el campo cantidad_total
            //  de los productos que lo requieran, por lo tanto ya se le puede mandar el importe al cliente
            //  si el cliente confirma el monto y la lista de productos como quedo finalmente se puede facturar
            //FACTURADO -> se genera la venta correspondiente, ya no se puede modificar el pedido 
            //  a partir de ahi solo se pueden hacer modificaciones en la venta asociada
            //  si esta FACTURADO entonces esta listo para entregar

            //cosas a tener en cuenta 
            //      -> un campo para definir si es envio o retira en el local
            //      -> un campo para comentarios
            //      -> un campo para fecha de envio acordada
            //      -> un campo para definir si se envia por la mañana o por la tarde
            //      -> un campo de usuario para saber quien lo preparo
            //      -> un campo para registrar la fecha en que se entrego?
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
