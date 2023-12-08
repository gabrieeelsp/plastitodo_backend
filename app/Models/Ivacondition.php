<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ivacondition extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    public $timestamps = false;

    public function modelofact()
    {
        return $this->belongsTo(Modelofact::class);
    }

    /* public function accept_modelofact($modelofact_id)
    {
        foreach($this->modelofacts as $modelofact){
            if($modelofact->id == $modelofact_id){
                return true;
            }
        }
        return false;
    } */
}
