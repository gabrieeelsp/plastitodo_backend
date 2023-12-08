<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'surname',
        'email',
        'password',
        'direccion',
        'telefono',
        'nombre_fact',
        'direccion_fact',
        'docnumber',
        'ivacondition_id',
        'doctype_id',
        'tipo',
        'is_fact_default',
        'tipo_persona',
        'coments_client',
        'coments_direccion_client',
        'credito_disponible',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function ivacondition() 
    {
        return $this->belongsTo(Ivacondition::class);
    }

    public function doctype() 
    {
        return $this->belongsTo(Doctype::class);
    }

    public function tiene_informacion_fe()
    {
        if(!$this->doctype){ return false; }
        if(!$this->docnumber){ return false; }
        if(!$this->direccion_fact){ return false; }
        if($this->tipo_persona == 'JURIDICA' && !$this->nombre_fact) { return false; }
        if($this->tipo_persona == 'FISICA' && !$this->surname) { return false; }
        return true;
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function catalogos()
    {
        return $this->belongsToMany(Catalogo::class);
    }
}
