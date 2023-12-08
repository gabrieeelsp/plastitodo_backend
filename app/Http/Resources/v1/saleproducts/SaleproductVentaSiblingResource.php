<?php

namespace App\Http\Resources\v1\saleproducts;

use Illuminate\Http\Resources\Json\JsonResource;

class SaleproductVentaSiblingResource extends JsonResource
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
		
	         'name' => $this->name,
		 'precio_min' => $this->precio_min,
		 'precio_may' => $this->precio_may,
		 'relacion_venta_stock' => $this->relacion_venta_stock,

		 'desc_min' => $this->desc_min,
		 'desc_may' => $this->desc_may,
		 'fecha_desc_desde' => $this->fecha_desc_desde,
		 'fecha_desc_hasta' => $this->fecha_desc_hasta,
	     ],
	];
    }
}
