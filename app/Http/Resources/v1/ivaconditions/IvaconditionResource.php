<?php

namespace App\Http\Resources\v1\ivaconditions;

use Illuminate\Http\Resources\Json\JsonResource;

class IvaconditionResource extends JsonResource
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
            'type' => 'ivaconditions',
            'attributes' => [
                'id_afip' => $this->id_afip,
                'name' => $this->name,
            ],
            'relationships' => [
                'modelofact' =>  [
                    "id" => $this->modelofact->id,
                    "attributes" => [
                        "name" => $this->modelofact->name
                    ]
                ]
            ]
        ];
    }
}
