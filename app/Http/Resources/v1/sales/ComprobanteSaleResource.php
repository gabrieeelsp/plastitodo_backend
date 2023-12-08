<?php

namespace App\Http\Resources\v1\sales;

use Illuminate\Http\Resources\Json\JsonResource;

use Carbon\Carbon;

class ComprobanteSaleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'attributes' => [
                'punto_venta' => $this->punto_venta,
                'numero' => $this->numero,
                'cae' => $this->cae,
                'cae_fch_vto' => $this->cae_fch_vto ? date('d / m / Y', Carbon::parse($this->cae_fch_vto)->timestamp) : null,
                'tipo' => $this->modelofact->name,
                'id_afip_tipo' => $this->id_afip_tipo,
                //'created_at' => date('d / m / Y', $this->created_at->timestamp),
                'created_at' => $this->created_at,

                'nombre_empresa' => $this->nombre_empresa,                
                'razon_social_empresa' => $this->razon_social_empresa,
                'domicilio_comercial_empresa' => $this->domicilio_comercial_empresa,
                'ivacondition_name_empresa' => $this->ivacondition_name_empresa,
                'cuit_empresa' => $this->cuit_empresa,
                'ing_brutos_empresa' => $this->ing_brutos_empresa,
                'fecha_inicio_act_empresa' => $this->fecha_inicio_act_empresa,                

                'nombre_fact_client' => $this->nombre_fact_client,
                'direccion_fact_client' => $this->direccion_fact_client,
                'ivacondition_name_client' => $this->ivacondition_name_client,
                'condicion_venta' => $this->condicion_venta,
                'docnumber_client' => $this->docnumber,
                'doctype_name_client' => $this->doctype_name,
                'doctype_id_afip_client' => $this->doctype_id_afip
                
            ],
            'relationships' => [
                'modelofact' => [
                    'id' => $this->modelofact->id,
                    'name' => $this->modelofact->name,
                ],
                
            ]       
            
        ]; 
    }
}
