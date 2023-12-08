<?php

namespace App\Http\Resources\v1\ivaaliquots;

use Illuminate\Http\Resources\Json\JsonResource;

class IvaaliquotResource extends JsonResource
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
            'type' => 'ivaaliquots',
            'attributes' => [
                'name' => $this->name,
                'valor' => $this->valor,
                'id_afip' => $this->id_afip,
                
            ]
        ];
    }
}
