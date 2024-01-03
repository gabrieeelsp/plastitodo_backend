<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\api\v1\AuthController;

use App\Http\Controllers\api\v1\StockproductController;
use App\Http\Controllers\api\v1\StockproductgroupController;
use App\Http\Controllers\api\v1\SaleproductController;
use App\Http\Controllers\api\v1\SaleproductgroupController;
use App\Http\Controllers\api\v1\PurchaseproductController;
use App\Http\Controllers\api\v1\ComboController;
use App\Http\Controllers\api\v1\SaleController;
use App\Http\Controllers\api\v1\IvaconditionController;
use App\Http\Controllers\api\v1\DoctypeController;
use App\Http\Controllers\api\v1\ClientController;
use App\Http\Controllers\api\v1\SupplierController;
use App\Http\Controllers\api\v1\EmpresaController;
use App\Http\Controllers\api\v1\SucursalController;

use App\Http\Controllers\api\v1\DeliveryshiftController;

use App\Http\Controllers\api\v1\IvaaliquotController;
use App\Http\Controllers\api\v1\ModelofactController;

use App\Http\Controllers\api\v1\ComprobanteController;

use App\Http\Controllers\api\v1\DevolutionController;
use App\Http\Controllers\api\v1\CreditnoteController;
use App\Http\Controllers\api\v1\DebitnoteController;
use App\Http\Controllers\api\v1\PaymentmethodController;

use App\Http\Controllers\api\v1\UserController;

use App\Http\Controllers\api\v1\CajaController;
use App\Http\Controllers\api\v1\PaymentController;
use App\Http\Controllers\api\v1\RefundController;

use App\Http\Controllers\api\v1\PurchaseorderController;

use App\Http\Controllers\api\v1\StockmovementController;

use App\Http\Controllers\api\v1\TagController;

use App\Http\Controllers\api\v1\CatalogoController;

use App\Http\Controllers\api\v1\ValorController;

use App\Http\Controllers\api\v1\FamiliaController;

use App\Http\Controllers\api\v1\OrderController;

use App\Http\Controllers\api\v1\StocktransferController;

use App\Http\Controllers\api\v1\InicioController;

use App\Http\Controllers\api\v1\StocksucursalController;

use App\Http\Controllers\api\v1\DashboardController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/* Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
 */
Route::middleware(['cors'])->prefix('v1')->group(static function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);

    Route::get('stockproducts/stock', [StockproductController::class,'get_stock']);
    Route::resource('stockproducts', StockproductController::class)->only(['index', 'show', 'update', 'store']);
    Route::resource('stockproductgroups', StockproductgroupController::class)->only(['index', 'show', 'update', 'store']);

    
    Route::put('stockproducts/{id}/update_values', [StockproductController::class, 'update_values']);
    Route::post('stockproducts/{id}/updload_image', [StockproductController::class, 'updload_image']);
    Route::put('stockproducts/{id}/remove_image', [StockproductController::class, 'remove_image']);
    Route::get('get_stockproducts_select', [StockproductController::class, 'get_stockproducts_select']);
    Route::get('saleproducts/search_barcode', [SaleproductController::class, 'search_barcode']);
    Route::get('s/{id}/g', [SaleproductController::class, 'get_saleproduct_siblings']);
    Route::resource('saleproducts', SaleproductController::class)->only(['index', 'show', 'update', 'store']);
    Route::resource('saleproductgroups', SaleproductgroupController::class)->only(['index', 'show', 'update', 'store']);
    Route::put('saleproducts/{id}/update_values', [SaleproductController::class, 'update_values']);
    Route::put('saleproducts/{id}/update_desc_values', [SaleproductController::class, 'update_desc_values']);
    Route::post('saleproducts/{id}/updload_image', [SaleproductController::class, 'updload_image']);
    Route::put('saleproducts/{id}/remove_image', [SaleproductController::class, 'remove_image']);
    Route::get('get_saleproducts_select', [SaleproductController::class, 'get_saleproducts_select']);

    Route::resource('purchaseproducts', PurchaseproductController::class)->only(['index', 'show', 'update', 'store']);
    
    Route::resource('combos', ComboController::class)->only(['index', 'show', 'store', 'update']);
    Route::put('combos/{id}/update_values', [ComboController::class, 'update_values']);
    Route::post('combos/{id}/updload_image', [ComboController::class, 'upload_image']);
    Route::put('combos/{id}/remove_image', [ComboController::class, 'remove_image']);
    Route::put('combos/{id}/update_configuration', [ComboController::class, 'update_configuration']);
    Route::get('combos/get_saleproduct/{id}', [ComboController::class, 'get_saleproduct']);


    Route::resource('familias', FamiliaController::class)->only(['index', 'show', 'update', 'store']);
    Route::put('familias/{id}/remove_image', [FamiliaController::class, 'remove_image']);
    Route::post('familias/{id}/updload_image', [FamiliaController::class, 'updload_image']);
    Route::get('get_familias_select', [FamiliaController::class, 'get_familias_select']);

    Route::get('get_stockproducts_order_by_stock', [StockproductController::class, 'get_stockproducts_order_by_stock']);
});

Route::middleware(['auth:sanctum', 'cors'])->prefix('v1')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::resource('purchaseorders', PurchaseorderController::class)->only(['index', 'show', 'update', 'destroy']);

    Route::resource('stockmovements', StockmovementController::class)->only(['index', 'show', 'update', 'destroy']);
    Route::post('stockmovements/new', [StockmovementController::class, 'new']);

    Route::resource('stocktransfers', StocktransferController::class)->only(['index', 'store', 'show', 'update', 'destroy']);

    Route::resource('empresas', EmpresaController::class)->only(['index', 'show']);
    Route::resource('sucursals', SucursalController::class)->only(['index', 'show', 'update', 'store']);
    Route::get('get_sucursals_select', [SucursalController::class, 'get_sucursals_select']);

    Route::get('orders/get_orders_distribucion', [OrderController::class, 'get_orders_distribucion']);
    Route::resource('orders', OrderController::class)->only(['index', 'update', 'show', 'store', 'destroy']);
    Route::put('orders/{id}/set_state', [OrderController::class, 'set_state']);
    Route::get('orders/{id}/get_order_check_sale', [OrderController::class, 'get_order_check_sale']);
    Route::put('orders/{id}/update_precios', [OrderController::class, 'update_precios']);
    


    Route::resource('sales', SaleController::class)->only(['index', 'show', 'store']);
    Route::get('/sales/{id}/make_devolution', [SaleController::class, 'make_devolution']);
    Route::get('get_sale_products_venta', [SaleproductController::class, 'get_sale_products_venta']);
    Route::resource('ivaconditions', IvaconditionController::class)->only(['index']);
    Route::resource('doctypes', DoctypeController::class)->only(['index']);
    Route::resource('clients', ClientController::class)->only(['index', 'update', 'show', 'store']);
    Route::get('get_clients_select', [ClientController::class, 'get_clients_select']);
    Route::post('suppliers/make_order', [SupplierController::class, 'make_order']);
    Route::resource('suppliers', SupplierController::class)->only(['index', 'update', 'show', 'store']);
    Route::get('get_suppliers_select', [SupplierController::class, 'get_suppliers_select']);
    

    Route::resource('ivaaliquots', IvaaliquotController::class)->only(['index']);
    Route::resource('deliveryshifts', DeliveryshiftController::class)->only(['index']);
    Route::resource('valors', ValorController::class)->only(['index']);
    Route::resource('modelofacts', ModelofactController::class)->only(['index']);

    Route::post('comprobantes/facts', [ComprobanteController::class, 'make_fact']);
    Route::post('comprobantes/nc', [ComprobanteController::class, 'make_nc']);
    Route::post('comprobantes/nd', [ComprobanteController::class, 'make_nd']);
    Route::post('comprobantes/nc_from_devolution', [ComprobanteController::class, 'make_nc_from_devolution']);

    Route::resource('devolutions', DevolutionController::class)->only(['store']);
    Route::resource('creditnotes', CreditnoteController::class)->only(['store']);
    Route::resource('debitnotes', DebitnoteController::class)->only(['store']);


    Route::resource('paymentmethods', PaymentmethodController::class)->only(['index']);

    Route::resource('users', UserController::class)->only(['index', 'store', 'show', 'update']);
    Route::put('users/{id}/update_password', [UserController::class, 'update_password']);

    Route::resource('cajas', CajaController::class)->only(['index', 'store','show']);
    Route::get('cajas/find/{id}', [CajaController::class, 'find']);
    Route::put('cajas/{id}/cerrar', [CajaController::class, 'cerrar']);
    
    Route::resource('payments', PaymentController::class)->only(['index', 'store']);
    Route::put('payments/{id}/confirm', [PaymentController::class, 'confirm']);
    Route::put('payments/{id}/no_confirm', [PaymentController::class, 'no_confirm']);

    Route::resource('refunds', RefundController::class)->only(['index', 'store']);
    Route::put('refunds/{id}/confirm', [RefundController::class, 'confirm']);
    Route::put('refunds/{id}/no_confirm', [RefundController::class, 'no_confirm']);

    Route::get('get_tags_select', [TagController::class, 'get_tags_select']);
    Route::resource('tags', TagController::class)->only(['index', 'update', 'show', 'store']);

    Route::get('get_catalogos_select', [CatalogoController::class, 'get_catalogos_select']);
    Route::resource('catalogos', CatalogoController::class)->only(['index', 'update', 'show', 'store']);

    Route::get('inicio_data', [InicioController::class, 'inicio_data']);

    Route::put('stocksucursals/update_values', [StocksucursalController::class, 'update_values']);

    Route::get('dashboard/ventas_dia', [DashboardController::class, 'ventas_dia']);
});
