<?php

namespace App\Http\Resources\v1\modelofacts;

use Illuminate\Http\Resources\Json\JsonResource;

class ModelofactResource extends JsonResource
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
            'type' => 'modelofacts',
            'attributes' => [
                'name' => $this->name,
                'monto_max_no_id_efectivo' => $this->monto_max_no_id_efectivo,
                'monto_max_no_id_no_efectivo' => $this->monto_max_no_id_no_efectivo,

                'id_afip_factura' => $this->id_afip_factura,
                'id_afip_nc' => $this->id_afip_nc,
                'id_afip_nd' => $this->id_afip_nd,
            ]
        ];
    }
}
