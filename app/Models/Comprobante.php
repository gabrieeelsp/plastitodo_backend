<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comprobante extends Model
{
    use HasFactory;

    public function modelofact()
    {
        return $this->belongsTo(Modelofact::class);
    }

    public function doctype()
    {
        return $this->belongsTo(Doctype::class);
    }

    public function comprobanteable()
    {
        return $this->morphTo();
    }

    public function is_autorizado()
    {
        if($this->cae){
            return true;
        }
        return false;
    }
}
