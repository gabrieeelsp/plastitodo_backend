<?php

namespace App\Http\Resources\v1\inicio;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Models\Paymentmethod;
use App\Http\Resources\v1\paymentmethods\PaymentmethodResource;

use App\Models\Ivaaliquot;
use App\Http\Resources\v1\ivaaliquots\IvaaliquotResource;

use App\Models\Modelofact;
use App\Http\Resources\v1\modelofacts\ModelofactResource;

use App\Models\Ivacondition;
use App\Http\Resources\v1\ivaconditions\IvaconditionResource;

use App\Models\Doctype;
use App\Http\Resources\v1\doctypes\DoctypeResource;

use App\Models\Sucursal;
use App\Http\Resources\v1\sucursals\SucursalResource;

use App\Models\Valor;
use App\Http\Resources\v1\valors\ValorResource;

use App\Models\Deliveryshift;
use App\Http\Resources\v1\deliveryshifts\DeliveryshiftResource;


use App\Models\Empresa;
use App\Http\Resources\v1\empresas\EmpresaResource;

class InicioResource extends JsonResource
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
            'paymentMethods' => PaymentmethodResource::collection(Paymentmethod::orderBy('name', 'ASC')->get()),

            'ivaaliquots' => IvaaliquotResource::collection(Ivaaliquot::orderBy('name', 'ASC')->get()),

            'modelofacts' => ModelofactResource::collection(Modelofact::orderBy('name', 'ASC')->get()),

            'ivaconditions' => IvaconditionResource::collection(Ivacondition::orderBy('name', 'ASC')->get()),

            'doctypes' => DoctypeResource::collection(Doctype::orderBy('name', 'ASC')->get()),

            'sucursals' => SucursalResource::collection(Sucursal::orderBy('name', 'ASC')->get()),

            'valors' => ValorResource::collection(Valor::orderBy('name', 'ASC')->get()),

            'deliveryshifts' => DeliveryshiftResource::collection(Deliveryshift::orderBy('name', 'ASC')->get()),

            'empresa' =>  new EmpresaResource(Empresa::find(1)),


        ];
    }
}
