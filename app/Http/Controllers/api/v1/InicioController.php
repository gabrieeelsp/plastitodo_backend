<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Resources\v1\inicio\InicioResource;

class InicioController extends Controller
{
    public function inicio_data ( ) 
    {
        return new InicioResource(null);
    }
}
