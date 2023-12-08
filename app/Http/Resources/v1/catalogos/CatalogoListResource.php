<?php

namespace App\Http\Resources\v1\catalogos;

use Illuminate\Http\Resources\Json\JsonResource;

class CatalogoListResource extends JsonResource
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
            'type' => 'catalogos',
            'attributes' => [
                'name' => $this->name,                
		        'color' => $this->color,
                'comments' => $this->comments ? $this->comments :  '',
            ],
            'relationships' => [
                'saleproducts' => null,
                'clients' => null,
            ],
        ]; 
    }
}
