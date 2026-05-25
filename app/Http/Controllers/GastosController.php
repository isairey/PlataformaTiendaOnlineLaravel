<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gastos;
use Carbon\Carbon;
class GastosController extends Controller
{
    //
    public function gastos (){
        return view ('Administrador/Gastos');
    }
    

    public function gastosTraer(Request $request)
    {
        // Obtén las fechas de inicio y fin desde la solicitud
        $fecha_inicial = $request->input('startDate');
        $fecha_final = $request->input('endDate');
        
        // Filtrar los gastos según las fechas, si ambas fechas están presentes
       
        $query = Gastos::query();
        
        if ($fecha_inicial && $fecha_final) {
            // Filtrar por fecha_registro entre las fechas proporcionadas
            $fecha_inicial = Carbon::createFromFormat('Y-m-d', $fecha_inicial)->startOfDay();
            $fecha_final = Carbon::createFromFormat('Y-m-d', $fecha_final)->endOfDay();
            $query->whereBetween('fecha_registro', [$fecha_inicial, $fecha_final]);
        }
        
        // Si no hay filtros de fechas, no se hace ningún ajuste a la consulta
        
        // Paginando los resultados
        $gastos = $query->paginate(10); // Puedes cambiar el 10 por el número de elementos por página que desees
    
        // Calcular la suma de todos los gastos
        $totalGastos = $gastos->sum('precio'); // Reemplaza 'precio' con el nombre del campo que contiene el valor de los gastos
        
        return response()->json([
            'pagination' => [
                'total' => $gastos->total(),
                'current_page' => $gastos->currentPage(),
                'per_page' => $gastos->perPage(),
                'last_page' => $gastos->lastPage(),
                'from' => $gastos->firstItem(),
                'to' => $gastos->lastItem(),
            ],
            'resultados' => $gastos->items(), // Array plano con todos los objetos de descripción
            'total_gastos' => $totalGastos, // Suma de todos los gastos
        ], 200);
    }
    
    
    


    public function gastosAgregar(Request $request)
    {
        $validated = $request->validate([
            'descripcion' => 'required|string|max:600',
            'precio' => 'required|string|min:0',
        ]);
        $precioLimpio = (int) str_replace(['$', '.', ' '], '',  $validated['precio']);
        $gasto = new Gastos();
        $gasto->descripcion = $validated['descripcion'];
        $gasto->precio =  $precioLimpio;
    
        $gasto->save();
    
    }

    public function eliminarGasto(Request $request, $id_gasto)
    {
        // Buscar el gasto en la base de datos usando el id recibido
        $gasto = Gastos::where('id_gasto', $id_gasto)->first();
  
    
        if (!$gasto) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gasto no encontrado',
            ], 404);
        }
    
        // Eliminar el gasto
        $gasto->delete();

    
        return response()->json([
            'status' => 'success',
            'message' => 'Gasto eliminado correctamente',
        ], 200);
    }
    
}
