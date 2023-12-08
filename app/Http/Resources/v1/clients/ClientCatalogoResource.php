<?php

namespace App\Http\Resources\v1\clients;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientCatalogoResource extends JsonResource
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
            ],
        ]; 
    }
}
