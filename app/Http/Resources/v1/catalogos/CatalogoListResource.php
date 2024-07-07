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
<<<<<<< HEAD
		'color' => $this->color,
		'key' => $this->key,
=======
		        'color' => $this->color,
                'key' => $this->key,
>>>>>>> ee958a6682413aa0298ce1e116b5e421d135ed46
                'comments' => $this->comments ? $this->comments :  '',
            ],
            'relationships' => [
                'saleproducts' => null,
                'clients' => null,
            ],
        ]; 
    }
}
