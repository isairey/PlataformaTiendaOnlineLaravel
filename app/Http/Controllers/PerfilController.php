<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Factura;
use App\Models\Producto;
use App\Models\Clientes;
use App\Models\GananciasDiarias;
use App\Models\ProductosModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PerfilController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request){

        return view ('Administrador/Perfil');
    }

    public function Rol() {//// sacra roles para hacer validacion
       
        $user = Auth::user();
    
        return response()->json([
            'rol' => $user ? $user->rol : null, 
        ]);
    }

    public function Nav(){
   
    $ProdutosVendidos = Producto::all();
    $TotalPorductosVentidos = count($ProdutosVendidos);


    /// canbtidad de clientes 
    $Clientes = Clientes::all();
    $CantidadClientes = count($Clientes);


    //sacra ganacias del mes 
    $fechaHoy = Carbon::today('America/Bogota');
    $mesActual = $fechaHoy->month;
    $anioActual = $fechaHoy->year;

  
    $gananciasDiarias = GananciasDiarias::whereDate('fecha_de_creacion', $fechaHoy)
    ->whereMonth('fecha_de_creacion', $mesActual)
    ->whereYear('fecha_de_creacion', $anioActual)
    ->sum('Total_G');


    /// productos vendidos hoy 
    $facturasHoy = Factura::whereDate('fecha_de_creacion', $fechaHoy)
    ->whereMonth('fecha_de_creacion', $mesActual)
    ->whereYear('fecha_de_creacion', $anioActual)
    ->get();
    

    $productosVendidos = [];

    foreach ($facturasHoy as $factura) {

        $productos = json_decode($factura->descripcion, true); 
        if (is_array($productos)) {
            $productosVendidos = array_merge($productosVendidos, $productos);
        }
    }

    $cantidadProductosVendidos = count($productosVendidos);

    $VnetasDelDia = $productosVendidos;


    return response()->json([
        'total_productos_vendidos' => $TotalPorductosVentidos,
        'cantidad_clientes' => $CantidadClientes,
        'ganancias_diarias' => $gananciasDiarias,
        'cantidad_productos_vendidos_hoy' => $cantidadProductosVendidos,
        'VnetasDelDia' =>  $VnetasDelDia
    ]);
    }

    public function getMonthlyData( ){
        try {

            $mesActual = date('n'); // Devuelve el mes actual (1-12)
            $anioActual = date('Y'); // Devuelve el año actual
    

            $results = DB::table('ganancias_diarias')
                ->select(
                    DB::raw('MONTH(fecha_de_creacion) as month'),
                    DB::raw('SUM(CAST(Total_G AS DECIMAL(10,2))) AS total_mensual')
                )
                ->whereYear('fecha_de_creacion', $anioActual) // Solo datos del año actual
                ->groupBy(DB::raw('MONTH(fecha_de_creacion)'))
                ->orderBy(DB::raw('MONTH(fecha_de_creacion)'))
                ->get();
        
                $data = [
                    'total_mensual' => array_fill(0, 12, 0),
                ];
            
                foreach ($results as $row) {
                    $monthIndex = $row->month - 1;
                    $data['total_mensual'][$monthIndex] = $row->total_mensual;
             
                }
            
                return response()->json($data);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Error al procesar la solicitud.'], 500);
            }
    
    }

   
    public function MejoresClientes(){

        $mejoresClientes = DB::table('clientes')
        ->join('factura', 'clientes.documento', '=', 'factura.documento')
        ->select('clientes.*', DB::raw('SUM(JSON_LENGTH(factura.descripcion)) as total_productos'))
        ->groupBy('clientes.id_cliente', 'clientes.nombre')
        ->orderByDesc('total_productos')
        ->having('total_productos', '>', 0) 
        ->limit(10)
        ->get();

      return $mejoresClientes;


    }
    
}
