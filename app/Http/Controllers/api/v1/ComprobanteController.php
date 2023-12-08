<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\Comprobante;
use App\Models\Sale;
use App\Models\Modelofact;
use App\Models\Ivacondition;
use App\Models\Ivaaliquot;
use App\Models\Devolution;
use App\Models\Creditnote;
use App\Models\Debitnote;
use Illuminate\Http\Request;

use App\Http\Resources\v1\sales\ComprobanteSaleResource;

use Carbon\Carbon;

use Afip;

class ComprobanteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Comprobante  $comprobante
     * @return \Illuminate\Http\Response
     */
    public function show(Comprobante $comprobante)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Comprobante  $comprobante
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Comprobante $comprobante)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Comprobante  $comprobante
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comprobante $comprobante)
    {
        //
    }

    public function verificar_comprobantes($punto_venta, $id_afip_tipo_comprobante, $afip)
    {
        
        $comprobanteEnviado = Comprobante::
            whereNull('cae')
            ->whereNotNull('numero')
            ->where('punto_venta', $punto_venta)
            ->where('id_afip_tipo', $id_afip_tipo_comprobante)
            ->first();
        // return $comprobanteEnviado;
        if ($comprobanteEnviado){ 
            $voucher_info = $afip->ElectronicBilling->getVoucherInfo($comprobanteEnviado->numero, $punto_venta, $id_afip_tipo_comprobante);
            
            // return $voucher_info;
            if ($voucher_info === null){ //El comprobante no existe
                $comprobanteEnviado->numero = null;
            }else {
                $comprobanteEnviado->cae = $voucher_info->CodAutorizacion;
                $comprobanteEnviado->cae_fch_vto = Carbon::parse($voucher_info->FchVto);
            }
            $comprobanteEnviado->save();
            //return $comprobanteEnviado;
        }
        return true;
    }

    public function make_fact(Request $request)
    {  
        //return $request->all();
        $sale = Sale::findOrFail($request->get('sale_id'));
        
        if($sale->comprobante && $sale->comprobante->is_autorizado()) { 
            return response()->json(['message' => 'El comprobante ya se encuentra generado.']);
        }

        if ( $request->get('ivacondition_id') == 0 && $sale->comprobante ) {
            $ivacondition = Ivacondition::where('name', 'LIKE', $sale->comprobante->ivacondition_name_client)->first();
        } else {
            $ivacondition = Ivacondition::findOrFail($request->get('ivacondition_id'));
        }
        

        //$afip = new Afip(array('CUIT' => 20291188568));
        $afip = new Afip(array('CUIT' => 30714071633, 'production' => true));
        
        $resp = $this->verificar_comprobantes($sale->sucursal->punto_venta_fe, $ivacondition->modelofact->id_afip_factura, $afip);
        
        if($sale->comprobante && $sale->comprobante->is_autorizado()){ //Este comprobante ya estaba autorizado
            return new ComprobanteSaleResource($sale->comprobante);
        }

        $is_pago_efectivo = $sale->hasPaymentCash();

        if(( !$sale->client ) || ($sale->client && !$sale->client->tiene_informacion_fe())){ // Sin cliente registrado
            if($is_pago_efectivo){
                if($sale->total >= $ivacondition->modelofact->monto_max_no_id_efectivo) {
                    return response()->json(['message' => 'No es posible realizar la operación. No existe información fiscal correspondiente.'], 422);
                }
            }else{ // no es pago efectivo
                if($sale->total >= $ivacondition->modelofact->monto_max_no_id_no_efectivo) {
                    return response()->json(['message' => 'No es posible realizar la operación. No existe información fiscal correspondiente.'], 422);
                }
            }
        }
        // todo -> verificar si el cliente quiere incluir sus datos


        

        if( $sale->client && $sale->client->tiene_informacion_fe() ) {
            if ( $sale->client->tipo_persona == 'FISICA') {
                $nombre = $sale->client->name.' '.$sale->client->surname;
            }else {
                $nombre = $sale->client->nombre_fact;
            }
            $numero_doc = $sale->client->docnumber;
            $id_afip_doctype = $sale->client->doctype->id_afip;
            $name_doctype = $sale->client->doctype->name;
            $direccion = $sale->client->direccion_fact;

        }else {
            $nombre = "";
            $numero_doc = "0";
            $id_afip_doctype = 99;
            $name_doctype = "Sin identificar";
            $direccion = "";
        }

            
        
        $numero_comprobante = $afip->ElectronicBilling->getLastVoucher($sale->sucursal->punto_venta_fe, $ivacondition->modelofact->id_afip_factura);

        $numero_comprobante = $numero_comprobante + 1;

        if(!$sale->comprobante){
            $comprobante = new Comprobante;

            $comprobante->punto_venta = $sale->sucursal->punto_venta_fe;
            $comprobante->id_afip_tipo = $ivacondition->modelofact->id_afip_factura;
            $comprobante->comprobanteable_id = $sale->id;
            $comprobante->comprobanteable_type = 'App\Models\Sale';
            $comprobante->modelofact_id = $ivacondition->modelofact->id;
            $comprobante->docnumber = $numero_doc;
            $comprobante->doctype_id_afip = $id_afip_doctype;
            $comprobante->doctype_name =  $name_doctype;


            $comprobante->nombre_empresa = $sale->sucursal->empresa->name;
            $comprobante->razon_social_empresa = $sale->sucursal->empresa->razon_social;
            $comprobante->domicilio_comercial_empresa = $sale->sucursal->empresa->domicilio_comercial;
            $comprobante->ivacondition_name_empresa = $sale->sucursal->empresa->ivacondition->name;
            $comprobante->cuit_empresa = $sale->sucursal->empresa->cuit;
            
            $comprobante->ing_brutos_empresa = $sale->sucursal->empresa->ing_brutos;
            $comprobante->fecha_inicio_act_empresa = $sale->sucursal->empresa->fecha_inicio_act;

            $comprobante->condicion_venta = $sale->getCondicionVenta();

            
            $comprobante->nombre_fact_client = $nombre;
            $comprobante->direccion_fact_client = $direccion;
            $comprobante->ivacondition_name_client = $ivacondition->name;


        }else {
            $comprobante = $sale->comprobante;
        }

        $comprobante->numero = $numero_comprobante;
        $comprobante->save();


        //--- mando a autorizar ---------

        //revisar la fecha, actualmente va a enviar la fecha de la venta
        //pero puede ser la fecha actual 
        //ver cuantos dias max pueden pasar antes de enviar a autorizar
        
        $ImpNeto = 0;
        $ImpTotConc = 0;
        $ImpOpEx = 0;
        $ivaaliquots_send = array();
        foreach(Ivaaliquot::all() as $ivaaliquot){
            $baseImpIva = $sale->getBaseImpIva($ivaaliquot->id);            
            if ( $baseImpIva ){

                if ($ivaaliquot->id_afip != 1 && $ivaaliquot->id_afip != 2) {
                    array_push($ivaaliquots_send, array(
                        'Id' 		=> $ivaaliquot->id_afip, // Id del tipo de IVA (5 para 21%)(ver tipos disponibles) 
                        'BaseImp' 	=> $baseImpIva, // Base imponible
                        'Importe' 	=> $sale->getImpIva($ivaaliquot->id) // Importe 
                    ) );
                }

                //guardo ImpTotConc para despues
                if ($ivaaliquot->id_afip == 1 ) { $ImpTotConc = $baseImpIva; }

                //guardo ImpOpEx para despues
                if ($ivaaliquot->id_afip == 2 ) { $ImpOpEx = $baseImpIva; }

                if (in_array($ivaaliquot->id_afip, [3, 4, 5, 6, 8, 9], false)){
                    $ImpNeto = $ImpNeto + $baseImpIva;
                }
            }
        }


        $ImpIVA = round($sale->total - ($ImpTotConc + $ImpOpEx + $ImpNeto), 2, PHP_ROUND_HALF_UP);

        

        $date = $sale->created_at->format('Ymd');

        //return floatval($sale->total);

        $data = array(
            'CantReg' 	=> 1,  // Cantidad de comprobantes a registrar
            'PtoVta' 	=> $sale->sucursal->punto_venta_fe,  // Punto de venta
            'CbteTipo' 	=> $ivacondition->modelofact->id_afip_factura,  // Tipo de comprobante (ver tipos disponibles) 
            'Concepto' 	=> 1,  // Concepto del Comprobante: (1)Productos, (2)Servicios, (3)Productos y Servicios
            'DocTipo' 	=> $id_afip_doctype, // Tipo de documento del comprador (99 consumidor final, ver tipos disponibles)
            'DocNro' 	=> $numero_doc,  // Número de documento del comprador (0 consumidor final)
            'CbteDesde' => $numero_comprobante,  // Número de comprobante o numero del primer comprobante en caso de ser mas de uno
            'CbteHasta' => $numero_comprobante,  // Número de comprobante o numero del último comprobante en caso de ser mas de uno
            'CbteFch' 	=> intval($date), // (Opcional) Fecha del comprobante (yyyymmdd) o fecha actual si es nulo
            'ImpTotal' 	=> floatval(round($sale->total, 2, PHP_ROUND_HALF_UP)), // Importe total del comprobante


            'ImpTotConc' 	=> $ImpTotConc,   // Importe neto no gravado
            'ImpNeto' 	=> $ImpNeto, // Importe neto gravado
            'ImpOpEx' 	=> $ImpOpEx,   // Importe exento de IVA
            'ImpIVA' 	=> $ImpIVA,  //Importe total de IVA
            'ImpTrib' 	=> 0,   //Importe total de tributos
            'MonId' 	=> 'PES', //Tipo de moneda usada en el comprobante (ver tipos disponibles)('PES' para pesos argentinos) 
            'MonCotiz' 	=> 1,     // Cotización de la moneda usada (1 para pesos argentinos)  
            'Iva' 		=> $ivaaliquots_send, 
        );

        //return $data;
        
        $res = $afip->ElectronicBilling->CreateVoucher($data, false);
        try {
            
        } catch(\Exception $e) {
            return $e;
            return new ComprobanteSaleResource($comprobante);
        }

        $comprobante->cae = $res['CAE'];
        $comprobante->cae_fch_vto = $res['CAEFchVto'];

        $comprobante->save();

        return new ComprobanteSaleResource($comprobante);
    }

    public function make_nc_from_devolution(Request $request) {
        $devolution = Devolution::findOrFail($request->get('devolution_id'));

        if($devolution->comprobante && $devolution->comprobante->is_autorizado()) {
            return response()->json(['message' => 'El comprobante ya se encuentra generado.']);
        }

        if(!$devolution->sale->comprobante || !$devolution->sale->comprobante->is_autorizado()){
            return response()->json(['message' => 'La venta asociada no posee una comprobante autorizado.']);
        }

        $modelofact = $devolution->sale->comprobante->modelofact;

        //$afip = new Afip(array('CUIT' => 20291188568));
        $afip = new Afip(array('CUIT' => 30714071633, 'production' => true));

        $resp = $this->verificar_comprobantes($devolution->sucursal->punto_venta_fe, $modelofact->id_afip_nc, $afip);

        if($devolution->comprobante && $devolution->comprobante->is_autorizado()) { //Este comprobante ya estaba autorizado
            return new ComprobanteResource($sale->comprobante);
        }

        $numero_comprobante = $afip->ElectronicBilling->getLastVoucher($devolution->sucursal->punto_venta_fe, $modelofact->id_afip_nc);

        $numero_comprobante = $numero_comprobante + 1;

        if(!$devolution->comprobante){
            $comprobante = new Comprobante;

            $comprobante->punto_venta = $devolution->sucursal->punto_venta_fe;
            $comprobante->id_afip_tipo = $modelofact->id_afip_nc;
            $comprobante->comprobanteable_id = $devolution->id;
            $comprobante->comprobanteable_type = 'App\Models\Devolution';
            $comprobante->modelofact_id = $modelofact->id;
            $comprobante->docnumber = $devolution->sale->comprobante->docnumber;
            $comprobante->doctype_id_afip = $devolution->sale->comprobante->doctype_id_afip;
            $comprobante->doctype_name =  $devolution->sale->comprobante->doctype_name;

            $comprobante->nombre_empresa = $devolution->sale->comprobante->nombre_empresa;
            $comprobante->razon_social_empresa = $devolution->sale->comprobante->razon_social_empresa;
            $comprobante->domicilio_comercial_empresa = $devolution->sale->comprobante->domicilio_comercial_empresa;
            $comprobante->ivacondition_name_empresa = $devolution->sale->comprobante->ivacondition_name_empresa;
            $comprobante->cuit_empresa = $devolution->sale->comprobante->cuit_empresa;
            
            $comprobante->ing_brutos_empresa = $devolution->sale->comprobante->ing_brutos_empresa;
            $comprobante->fecha_inicio_act_empresa = $devolution->sale->comprobante->fecha_inicio_act_empresa;

            $comprobante->condicion_venta = $devolution->sale->comprobante->condicion_venta;

            $comprobante->nombre_fact_client = $devolution->sale->comprobante->nombre_fact_client;
            $comprobante->direccion_fact_client = $devolution->sale->comprobante->direccion_fact_client;
            $comprobante->ivacondition_name_client = $devolution->sale->comprobante->ivacondition_name_client;
        }else {
            $comprobante = $devolution->comprobante;
        }

        $comprobante->numero = $numero_comprobante;
        $comprobante->save();

        //--- mando a autorizar ---------

        //revisar la fecha, actualmente va a enviar la fecha de la venta
        //pero puede ser la fecha actual 
        //ver cuantos dias max pueden pasar antes de enviar a autorizar

        $ImpNeto = 0;
        $ImpTotConc = 0;
        $ImpOpEx = 0;
        $ivaaliquots_send = array();
        foreach(Ivaaliquot::all() as $ivaaliquot){
            $baseImpIva = $devolution->getBaseImpIva($ivaaliquot->id);            
            if ( $baseImpIva ){

                if ($ivaaliquot->id_afip != 1 && $ivaaliquot->id_afip != 2) {
                    array_push($ivaaliquots_send, array(
                        'Id' 		=> $ivaaliquot->id_afip, // Id del tipo de IVA (5 para 21%)(ver tipos disponibles) 
                        'BaseImp' 	=> $baseImpIva, // Base imponible
                        'Importe' 	=> $devolution->getImpIva($ivaaliquot->id) // Importe 
                    ) );
                }

                //guardo ImpTotConc para despues
                if ($ivaaliquot->id_afip == 1 ) { $ImpTotConc = $baseImpIva; }

                //guardo ImpOpEx para despues
                if ($ivaaliquot->id_afip == 2 ) { $ImpOpEx = $baseImpIva; }

                if (in_array($ivaaliquot->id_afip, [3, 4, 5, 6, 8, 9], false)){
                    $ImpNeto = $ImpNeto + $baseImpIva;
                }
            }
        }


        $ImpIVA = round($devolution->total - ($ImpTotConc + $ImpOpEx + $ImpNeto), 2, PHP_ROUND_HALF_UP);

        $date = $devolution->created_at->format('Ymd');

        

        $data = array(
            'CantReg' 	=> 1,  // Cantidad de comprobantes a registrar
            'PtoVta' 	=> $devolution->sucursal->punto_venta_fe,  // Punto de venta
            'CbteTipo' 	=> $modelofact->id_afip_nc,  // Tipo de comprobante (ver tipos disponibles) 
            'Concepto' 	=> 1,  // Concepto del Comprobante: (1)Productos, (2)Servicios, (3)Productos y Servicios
            'DocTipo' 	=> $devolution->sale->comprobante->doctype_id_afip, // Tipo de documento del comprador (99 consumidor final, ver tipos disponibles)
            'DocNro' 	=> $devolution->sale->comprobante->docnumber,  // Número de documento del comprador (0 consumidor final)
            'CbteDesde' => $numero_comprobante,  // Número de comprobante o numero del primer comprobante en caso de ser mas de uno
            'CbteHasta' => $numero_comprobante,  // Número de comprobante o numero del último comprobante en caso de ser mas de uno
            'CbteFch' 	=> intval($date), // (Opcional) Fecha del comprobante (yyyymmdd) o fecha actual si es nulo
            'ImpTotal' 	=> floatval($devolution->total), // Importe total del comprobante


            'ImpTotConc' 	=> $ImpTotConc,   // Importe neto no gravado
            'ImpNeto' 	=> $ImpNeto, // Importe neto gravado
            'ImpOpEx' 	=> $ImpOpEx,   // Importe exento de IVA
            'ImpIVA' 	=> $ImpIVA,  //Importe total de IVA
            'ImpTrib' 	=> 0,   //Importe total de tributos
            'MonId' 	=> 'PES', //Tipo de moneda usada en el comprobante (ver tipos disponibles)('PES' para pesos argentinos) 
            'MonCotiz' 	=> 1,     // Cotización de la moneda usada (1 para pesos argentinos)  
            'Iva' 		=> $ivaaliquots_send, 
            'CbtesAsoc' 	=> array( // (Opcional) Comprobantes asociados
                array(
                    'Tipo' 		=> $devolution->sale->comprobante->id_afip_tipo, // Tipo de comprobante (ver tipos disponibles) 
                    'PtoVta' 	=> $devolution->sale->comprobante->punto_venta, // Punto de venta
                    'Nro' 		=> $devolution->sale->comprobante->numero, // Numero de comprobante
                    //'Cuit' 		=> 20111111112 // (Opcional) Cuit del emisor del comprobante
                    )
                ),
        );
        //return $data;
        $res = $afip->ElectronicBilling->CreateVoucher($data, false);
        try {
            
        } catch(\Exception $e) {
            return new ComprobanteSaleResource($comprobante);
        }

        $comprobante->cae = $res['CAE'];
        $comprobante->cae_fch_vto = $res['CAEFchVto'];

        $comprobante->save();

        return new ComprobanteSaleResource($comprobante);
    }

    public function make_nc(Request $request)
    {
 
        $creditnote = Creditnote::findOrFail($request->get('creditnote_id'));

        if($creditnote->comprobante && $creditnote->comprobante->is_autorizado()) {
            return response()->json(['message' => 'El comprobante ya se encuentra generado.']);
        }

        if(!$creditnote->sale->comprobante || !$creditnote->sale->comprobante->is_autorizado()){
            return response()->json(['message' => 'La venta asociada no posee una comprobante autorizado.']);
        }

        $modelofact = $creditnote->sale->comprobante->modelofact;

        //$afip = new Afip(array('CUIT' => 20291188568));
        $afip = new Afip(array('CUIT' => 30714071633, 'production' => true));

        $resp = $this->verificar_comprobantes($creditnote->sucursal->punto_venta_fe, $modelofact->id_afip_nc, $afip);

        if($creditnote->comprobante && $creditnote->comprobante->is_autorizado()) { //Este comprobante ya estaba autorizado
            return new ComprobanteResource($sale->comprobante);
        }

        $numero_comprobante = $afip->ElectronicBilling->getLastVoucher($creditnote->sucursal->punto_venta_fe, $modelofact->id_afip_nc);

        $numero_comprobante = $numero_comprobante + 1;

        if(!$creditnote->comprobante){
            $comprobante = new Comprobante;

            $comprobante->punto_venta = $creditnote->sucursal->punto_venta_fe;
            $comprobante->id_afip_tipo = $modelofact->id_afip_nc;
            $comprobante->comprobanteable_id = $creditnote->id;
            $comprobante->comprobanteable_type = 'App\Models\Creditnote';
            $comprobante->modelofact_id = $modelofact->id;
            $comprobante->docnumber = $creditnote->sale->comprobante->docnumber;
            //$comprobante->doctype_id = $creditnote->sale->comprobante->doctype_id;
            $comprobante->doctype_id_afip = $creditnote->sale->comprobante->doctype_id_afip;
            $comprobante->doctype_name =  $creditnote->sale->comprobante->doctype_name;

            $comprobante->nombre_empresa = $creditnote->sale->comprobante->nombre_empresa;
            $comprobante->razon_social_empresa = $creditnote->sale->comprobante->razon_social_empresa;
            $comprobante->domicilio_comercial_empresa = $creditnote->sale->comprobante->domicilio_comercial_empresa;
            $comprobante->ivacondition_name_empresa = $creditnote->sale->comprobante->ivacondition_name_empresa;
            $comprobante->cuit_empresa = $creditnote->sale->comprobante->cuit_empresa;
            
            $comprobante->ing_brutos_empresa = $creditnote->sale->comprobante->ing_brutos_empresa;
            $comprobante->fecha_inicio_act_empresa = $creditnote->sale->comprobante->fecha_inicio_act_empresa;

            $comprobante->condicion_venta = $creditnote->sale->comprobante->condicion_venta;
            //$comprobante->doctype_id = $devolution->sale->comprobante->doctype_id;


            $comprobante->nombre_fact_client = $creditnote->sale->comprobante->nombre_fact_client;
            $comprobante->direccion_fact_client = $creditnote->sale->comprobante->direccion_fact_client;
            $comprobante->ivacondition_name_client = $creditnote->sale->comprobante->ivacondition_name_client;
        }else {
            $comprobante = $creditnote->comprobante;
        }

        $comprobante->numero = $numero_comprobante;
        $comprobante->save();

        //--- mando a autorizar ---------

        //revisar la fecha, actualmente va a enviar la fecha de la venta
        //pero puede ser la fecha actual 
        //ver cuantos dias max pueden pasar antes de enviar a autorizar

        $ImpNeto = 0;
        $ImpTotConc = 0;
        $ImpOpEx = 0;
        $ivaaliquots_send = array();
        foreach(Ivaaliquot::all() as $ivaaliquot){
            $baseImpIva = $creditnote->getBaseImpIva($ivaaliquot->id);            
            if ( $baseImpIva ){

                if ($ivaaliquot->id_afip != 1 && $ivaaliquot->id_afip != 2) {
                    array_push($ivaaliquots_send, array(
                        'Id' 		=> $ivaaliquot->id_afip, // Id del tipo de IVA (5 para 21%)(ver tipos disponibles) 
                        'BaseImp' 	=> $baseImpIva, // Base imponible
                        'Importe' 	=> $creditnote->getImpIva($ivaaliquot->id) // Importe 
                    ) );
                }

                //guardo ImpTotConc para despues
                if ($ivaaliquot->id_afip == 1 ) { $ImpTotConc = $baseImpIva; }

                //guardo ImpOpEx para despues
                if ($ivaaliquot->id_afip == 2 ) { $ImpOpEx = $baseImpIva; }

                if (in_array($ivaaliquot->id_afip, [3, 4, 5, 6, 8, 9], false)){
                    $ImpNeto = $ImpNeto + $baseImpIva;
                }
            }
        }


        $ImpIVA = round($creditnote->total - ($ImpTotConc + $ImpOpEx + $ImpNeto), 2, PHP_ROUND_HALF_UP);

        $date = $creditnote->created_at->format('Ymd');

        

        $data = array(
            'CantReg' 	=> 1,  // Cantidad de comprobantes a registrar
            'PtoVta' 	=> $creditnote->sucursal->punto_venta_fe,  // Punto de venta
            'CbteTipo' 	=> $modelofact->id_afip_nc,  // Tipo de comprobante (ver tipos disponibles) 
            'Concepto' 	=> 1,  // Concepto del Comprobante: (1)Productos, (2)Servicios, (3)Productos y Servicios
            'DocTipo' 	=> $creditnote->sale->comprobante->doctype_id_afip, // Tipo de documento del comprador (99 consumidor final, ver tipos disponibles)
            'DocNro' 	=> $creditnote->sale->comprobante->docnumber,  // Número de documento del comprador (0 consumidor final)
            'CbteDesde' => $numero_comprobante,  // Número de comprobante o numero del primer comprobante en caso de ser mas de uno
            'CbteHasta' => $numero_comprobante,  // Número de comprobante o numero del último comprobante en caso de ser mas de uno
            'CbteFch' 	=> intval($date), // (Opcional) Fecha del comprobante (yyyymmdd) o fecha actual si es nulo
            'ImpTotal' 	=> floatval($creditnote->total), // Importe total del comprobante


            'ImpTotConc' 	=> $ImpTotConc,   // Importe neto no gravado
            'ImpNeto' 	=> $ImpNeto, // Importe neto gravado
            'ImpOpEx' 	=> $ImpOpEx,   // Importe exento de IVA
            'ImpIVA' 	=> $ImpIVA,  //Importe total de IVA
            'ImpTrib' 	=> 0,   //Importe total de tributos
            'MonId' 	=> 'PES', //Tipo de moneda usada en el comprobante (ver tipos disponibles)('PES' para pesos argentinos) 
            'MonCotiz' 	=> 1,     // Cotización de la moneda usada (1 para pesos argentinos)  
            'Iva' 		=> $ivaaliquots_send, 
            'CbtesAsoc' 	=> array( // (Opcional) Comprobantes asociados
                array(
                    'Tipo' 		=> $creditnote->sale->comprobante->id_afip_tipo, // Tipo de comprobante (ver tipos disponibles) 
                    'PtoVta' 	=> $creditnote->sale->comprobante->punto_venta, // Punto de venta
                    'Nro' 		=> $creditnote->sale->comprobante->numero, // Numero de comprobante
                    //'Cuit' 		=> 20111111112 // (Opcional) Cuit del emisor del comprobante
                    )
                ),
        );

        $res = $afip->ElectronicBilling->CreateVoucher($data, false);
        try {
            
        } catch(\Exception $e) {
            return new ComprobanteSaleResource($comprobante);
        }

        $comprobante->cae = $res['CAE'];
        $comprobante->cae_fch_vto = $res['CAEFchVto'];

        $comprobante->save();

        return new ComprobanteSaleResource($comprobante);
    }

    public function make_nd(Request $request)
    {
        $debitnote = Debitnote::findOrFail($request->get('debitnote_id'));

        if($debitnote->comprobante && $debitnote->comprobante->is_autorizado()) {
            return response()->json(['message' => 'El comprobante ya se encuentra generado.']);
        }

        if(!$debitnote->sale->comprobante || !$debitnote->sale->comprobante->is_autorizado()){
            return response()->json(['message' => 'La venta asociada no posee una comprobante autorizado.']);
        }

        $modelofact = $debitnote->sale->comprobante->modelofact;

        //$afip = new Afip(array('CUIT' => 20291188568));
        $afip = new Afip(array('CUIT' => 30714071633, 'production' => true));

        $resp = $this->verificar_comprobantes($debitnote->sucursal->punto_venta_fe, $modelofact->id_afip_nd, $afip);

        if($debitnote->comprobante && $debitnote->comprobante->is_autorizado()) { //Este comprobante ya estaba autorizado
            return new ComprobanteResource($sale->comprobante);
        }

        $numero_comprobante = $afip->ElectronicBilling->getLastVoucher($debitnote->sucursal->punto_venta_fe, $modelofact->id_afip_nd);

        $numero_comprobante = $numero_comprobante + 1;

        if(!$debitnote->comprobante){
            $comprobante = new Comprobante;

            $comprobante->punto_venta = $debitnote->sucursal->punto_venta_fe;
            $comprobante->id_afip_tipo = $modelofact->id_afip_nd;
            $comprobante->comprobanteable_id = $debitnote->id;
            $comprobante->comprobanteable_type = 'App\Models\Debitnote';
            $comprobante->modelofact_id = $modelofact->id;
            $comprobante->docnumber = $debitnote->sale->comprobante->docnumber;
            $comprobante->doctype_id_afip = $debitnote->sale->comprobante->doctype_id_afip;
            $comprobante->doctype_name =  $debitnote->sale->comprobante->doctype_name;

            $comprobante->nombre_empresa = $debitnote->sale->comprobante->nombre_empresa;
            $comprobante->razon_social_empresa = $debitnote->sale->comprobante->razon_social_empresa;
            $comprobante->domicilio_comercial_empresa = $debitnote->sale->comprobante->domicilio_comercial_empresa;
            $comprobante->ivacondition_name_empresa = $debitnote->sale->comprobante->ivacondition_name_empresa;
            $comprobante->cuit_empresa = $debitnote->sale->comprobante->cuit_empresa;
            
            $comprobante->ing_brutos_empresa = $debitnote->sale->comprobante->ing_brutos_empresa;
            $comprobante->fecha_inicio_act_empresa = $debitnote->sale->comprobante->fecha_inicio_act_empresa;

            $comprobante->condicion_venta = $debitnote->sale->comprobante->condicion_venta;
            //$comprobante->doctype_id = $devolution->sale->comprobante->doctype_id;


            $comprobante->nombre_fact_client = $debitnote->sale->comprobante->nombre_fact_client;
            $comprobante->direccion_fact_client = $debitnote->sale->comprobante->direccion_fact_client;
            $comprobante->ivacondition_name_client = $debitnote->sale->comprobante->ivacondition_name_client;
        }else {
            $comprobante = $debitnote->comprobante;
        }

        $comprobante->numero = $numero_comprobante;
        $comprobante->save();

        //--- mando a autorizar ---------

        //revisar la fecha, actualmente va a enviar la fecha de la venta
        //pero puede ser la fecha actual 
        //ver cuantos dias max pueden pasar antes de enviar a autorizar

        $ImpNeto = 0;
        $ImpTotConc = 0;
        $ImpOpEx = 0;
        $ivaaliquots_send = array();
        foreach(Ivaaliquot::all() as $ivaaliquot){
            $baseImpIva = $debitnote->getBaseImpIva($ivaaliquot->id);            
            if ( $baseImpIva ){

                if ($ivaaliquot->id_afip != 1 && $ivaaliquot->id_afip != 2) {
                    array_push($ivaaliquots_send, array(
                        'Id' 		=> $ivaaliquot->id_afip, // Id del tipo de IVA (5 para 21%)(ver tipos disponibles) 
                        'BaseImp' 	=> $baseImpIva, // Base imponible
                        'Importe' 	=> $debitnote->getImpIva($ivaaliquot->id) // Importe 
                    ) );
                }

                //guardo ImpTotConc para despues
                if ($ivaaliquot->id_afip == 1 ) { $ImpTotConc = $baseImpIva; }

                //guardo ImpOpEx para despues
                if ($ivaaliquot->id_afip == 2 ) { $ImpOpEx = $baseImpIva; }

                if (in_array($ivaaliquot->id_afip, [3, 4, 5, 6, 8, 9], false)){
                    $ImpNeto = $ImpNeto + $baseImpIva;
                }
            }
        }


        $ImpIVA = round($debitnote->total - ($ImpTotConc + $ImpOpEx + $ImpNeto), 2, PHP_ROUND_HALF_UP);

        $date = $debitnote->created_at->format('Ymd');

        

        $data = array(
            'CantReg' 	=> 1,  // Cantidad de comprobantes a registrar
            'PtoVta' 	=> $debitnote->sucursal->punto_venta_fe,  // Punto de venta
            'CbteTipo' 	=> $modelofact->id_afip_nd,  // Tipo de comprobante (ver tipos disponibles) 
            'Concepto' 	=> 1,  // Concepto del Comprobante: (1)Productos, (2)Servicios, (3)Productos y Servicios
            'DocTipo' 	=> $debitnote->sale->comprobante->doctype_id_afip, // Tipo de documento del comprador (99 consumidor final, ver tipos disponibles)
            'DocNro' 	=> $debitnote->sale->comprobante->docnumber,  // Número de documento del comprador (0 consumidor final)
            'CbteDesde' => $numero_comprobante,  // Número de comprobante o numero del primer comprobante en caso de ser mas de uno
            'CbteHasta' => $numero_comprobante,  // Número de comprobante o numero del último comprobante en caso de ser mas de uno
            'CbteFch' 	=> intval($date), // (Opcional) Fecha del comprobante (yyyymmdd) o fecha actual si es nulo
            'ImpTotal' 	=> floatval($debitnote->total), // Importe total del comprobante


            'ImpTotConc' 	=> $ImpTotConc,   // Importe neto no gravado
            'ImpNeto' 	=> $ImpNeto, // Importe neto gravado
            'ImpOpEx' 	=> $ImpOpEx,   // Importe exento de IVA
            'ImpIVA' 	=> $ImpIVA,  //Importe total de IVA
            'ImpTrib' 	=> 0,   //Importe total de tributos
            'MonId' 	=> 'PES', //Tipo de moneda usada en el comprobante (ver tipos disponibles)('PES' para pesos argentinos) 
            'MonCotiz' 	=> 1,     // Cotización de la moneda usada (1 para pesos argentinos)  
            'Iva' 		=> $ivaaliquots_send, 
            'CbtesAsoc' 	=> array( // (Opcional) Comprobantes asociados
                array(
                    'Tipo' 		=> $debitnote->sale->comprobante->id_afip_tipo, // Tipo de comprobante (ver tipos disponibles) 
                    'PtoVta' 	=> $debitnote->sale->comprobante->punto_venta, // Punto de venta
                    'Nro' 		=> $debitnote->sale->comprobante->numero, // Numero de comprobante
                    //'Cuit' 		=> 20111111112 // (Opcional) Cuit del emisor del comprobante
                    )
                ),
        );

        $res = $afip->ElectronicBilling->CreateVoucher($data, false);
        try {
            
        } catch(\Exception $e) {
            return new ComprobanteSaleResource($comprobante);
        }

        $comprobante->cae = $res['CAE'];
        $comprobante->cae_fch_vto = $res['CAEFchVto'];

        $comprobante->save();

        return new ComprobanteSaleResource($comprobante);
    }
}
