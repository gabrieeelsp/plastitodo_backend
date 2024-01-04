<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Sale;
use App\Models\User;

use Carbon\Carbon;

class DashboardController extends Controller
{
    public function ventas_dia (Request $request)
    {
        $range = 'day';
        if($request->has('range')){
            $range = $request->get('range');
        }

        $atr = [];

        if ( $request->has('sucursal_id')){
            array_push($atr, ['sucursal_id', '=', $request->get('sucursal_id')] );
        }

        if ( auth()->user()->role == 'VENDEDOR' ) {
            array_push($atr, ['user_id', '=', auth()->user()->id] );
        }
        
        if ( $range == "month" ) {
            $ventas = Sale::where('created_at', '>=', Carbon::now()->startOfMonth()->toDateString())
                ->where($atr)
                ->get();  
        }
        else {
            $from = (new Carbon)->now()->subDays(0)->format('Y-m-d')." 00:00:00";
            $to = (new Carbon)->now()->subDays(0)->format('Y-m-d')." 23:59:59";
            //return [$from, $to];
            $ventas = Sale::whereBetween('created_at', [$from, $to])
                ->where($atr)
                ->get();    
        }


        // ********************* procesar ventas *******************//

        $array_users = array();
        $users = User::whereNotNull('role')->get();
        foreach ( $users as $user ) {
            $array_users[$user->id] = array($user->id, 0, $user->name, $user->surname);
        }

        $venta_total = 0;
        $facturacion_total = 0;

        $venta_total_mayorista = 0;
        $venta_total_minorista = 0;


        foreach ( $ventas as $venta ) {
            $total = $venta->total;
            $facturacion = 0;
            
            if ( $venta->comprobante ) {
                $facturacion = round($facturacion + $venta->total, 2, PHP_ROUND_HALF_UP);
            }

            foreach ( $venta->creditnotes as $creditnote ) {
                $total = round($total - $creditnote->total, 2, PHP_ROUND_HALF_UP);
                if ( $creditnote->comprobante ) {
                    $facturacion = round($facturacion - $creditnote->total, 2, PHP_ROUND_HALF_UP);
                }
            }

            foreach ( $venta->devolutions as $devolution ) {
                $total = round($total - $devolution->total, 2, PHP_ROUND_HALF_UP);
                if ( $devolution->comprobante ) {
                    $facturacion = round($facturacion - $devolution->total, 2, PHP_ROUND_HALF_UP);
                }
            }

            foreach ( $venta->debitnotes as $debitnote ) {
                $total = round($total + $debitnote->total, 2, PHP_ROUND_HALF_UP);
                if ( $debitnote->comprobante ) {
                    $facturacion = round($facturacion + $debitnote->total, 2, PHP_ROUND_HALF_UP);
                }
            }

            $venta_total = round($venta_total + $total, 2, PHP_ROUND_HALF_UP);
            $facturacion_total = round($facturacion_total + $facturacion, 2, PHP_ROUND_HALF_UP);
            
            $array_users[$venta->user_id][1] = round($array_users[$venta->user_id][1] + $total, 2, PHP_ROUND_HALF_UP);
            

            if ( $venta->client_id ) {
                if ( $venta->client->tipo == 'MAYORISTA' ) {
                    $venta_total_mayorista = round($venta_total_mayorista + $total, 2, PHP_ROUND_HALF_UP);    
                } else {
                    $venta_total_minorista = round($venta_total_minorista + $total, 2, PHP_ROUND_HALF_UP);
                }
            } else {
                $venta_total_minorista = round($venta_total_minorista + $total, 2, PHP_ROUND_HALF_UP);
            }

        }
        $dashboard = array();
        $dashboard['venta_total'] = $venta_total;
        $dashboard['venta_total_minorista'] = $venta_total_minorista;
        $dashboard['venta_total_mayorista'] = $venta_total_mayorista;

        $dashboard['facturacion'] = $facturacion_total;

        $array_users_filtered = array();
        foreach ( $array_users as $user ) {
            if ( $user[1] !== 0 ) {
                $u = array();
                $u['id'] = $user[0];
                $u['venta_total'] = $user[1];
                $u['name'] = $user[2];
                $u['surname'] = $user[3];
                array_push($array_users_filtered, $u);
            }
        }

        $dashboard['users'] = $array_users_filtered;
        return response()->json(["dashboard" => $dashboard]);
    }
}
