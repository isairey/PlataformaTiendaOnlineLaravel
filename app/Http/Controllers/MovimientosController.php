<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GananciasDiarias;

class MovimientosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function Index(Request $request){
        return view ('Administrador/movimiento');
    }
    // fucnion  para scara los movimitnos d elos clinete spor medio de su documento!!! 
    public function SacarMovimientos(Request $request)
    {
        $documento = $request->Buscar;
        $mp = $request->Seleccionado;
    
        $query = GananciasDiarias::join('clientes', 'ganancias_diarias.documento', '=', 'clientes.documento')
                                 ->where('ganancias_diarias.documento', $documento)
                                 ->select('ganancias_diarias.*', 'clientes.nombre');
        if ($mp !== null) {
            $query->where('ganancias_diarias.mp', $mp); // Aquí deberías ajustar según el campo que corresponde a 'mp'
        }
        $informacion = $query->paginate(16);
        // Devolver la respuesta
        return response()->json([
            'data' => $informacion->items(),
            'pagination' => [
                'total'        => $informacion->total(),
                'current_page' => $informacion->currentPage(),
                'per_page'     => $informacion->perPage(),
                'last_page'    => $informacion->lastPage(),
                'from'         => $informacion->firstItem(),
                'to'           => $informacion->lastItem(),
            ],
        ]);
    }
    
    
}
