<?php

namespace App\Http\Controllers;
use App\Models\Clientes;
use App\Models\Factura;
use App\Models\GananciasDiarias;
use App\Models\CuentasPendientes;
use Illuminate\Http\Request;
use App\Mail\EnviarCorreo;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
class ClientesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function clientes(Request $request)
    {

        return view('Administrador/clientes');
    }
    public function listar(Request $request)
    {
        $clientes = Clientes::all();
        $clientesConDeuda = $clientes->map(function ($cliente) {
            $totalDeuda = Factura::where('documento', $cliente->documento)
                ->sum('debe'); 
            $cliente->total_deuda = $totalDeuda;
            return $cliente;
        });
        return response()->json($clientesConDeuda);
    }
    
    public function actualizarCliente(Request $request, $id_cliente)
    {
        $cliente = Clientes::find($id_cliente);
    
        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }
    
        $validacion = $request->validate([
            'nombre' => 'required|string|max:255',
            'documento' => 'required|integer|min:1000000|max:999999999999', 
        ]);
        $cliente->nombre = $validacion['nombre'];
        $cliente->documento = $validacion['documento']; 
        $cliente->save();
    
        return response()->json(['message' => 'Cliente actualizado correctamente'], 200);
    }
    public function eliminarCliente($id_cliente)
    {
            $cliente = Clientes::find($id_cliente);
            if (!$cliente) {
                return response()->json([
                    'message' => 'cliente no encontrado'
                ], 404);
            }
            $cliente->delete();

    
    }
    public function buscarCliente(Request $request)
    {
        $query = $request->input('query');
        
        // Busca en nombre o documento
        $clientes = Clientes::where('nombre', 'like', '%' . $query . '%')
            ->orWhere('documento', 'like', '%' . $query . '%')
            ->get();
        
        return response()->json($clientes);
    }
     public function datosClientesAbono(Request $request){
            $id_cliente = $request->id_cliente; 
            
            $cliente = Clientes::select('documento', 'nombre')->where('id_cliente', $id_cliente)->first();
            if ($cliente) {
                $sumaDebe = Factura::where('documento', $cliente->documento)
                ->where('mp', 3)
                ->sum('debe');


                return response()->json([
                    'documento' => $cliente->documento,
                    'nombre' => $cliente->nombre,
                    'suma_debe' => $sumaDebe
                ]);

        }
    }
    public function Abono(Request $request){
    $Abono = $request->Abono;
    $documento = $request->documentoAbono;
    $facturas = Factura::where('documento', $documento)
    ->where('mp', 3)
    ->get();
    $GananciasD = new GananciasDIarias();
    $GananciasD->Total_G = $Abono;
    $GananciasD->documento = $documento; 
    $GananciasD->mp = 4; 
    $GananciasD->save();

    $Abonos = new CuentasPendientes();
    $Abonos->documento = $documento; 
    $Abonos->abono = $Abono;
    $Abonos->save();

    foreach ($facturas as $factura) {
        if ($Abono <= 0) {
            break; 
        }
        $debeActual = $factura->debe;
        if ($debeActual > 0) {
            $nuevoSaldo = max(0, $debeActual - $Abono);
            $abonoAplicado = $debeActual - $nuevoSaldo;
            $factura->debe = $nuevoSaldo;
            if ($nuevoSaldo == 0) {
                $factura->mp = 4;
                $AbonosClientes = DB::table('cuentas_pendientes')
                ->where('documento', $documento)
                ->update(['estado' => 1]);
            }
            $factura->save(); 
            $Abono -= $abonoAplicado;
        }
    }
                
            if ($Abono > 0) {
                return response()->json(['error' => 'El abono no se pudo aplicar completamente'], 400);
            }
            
            // Respuesta exitosa
            return response()->json(['success' => 'Abono aplicado correctamente'], 200);
            
    }
    public function AbonoTotal(Request $request){
        $documento = $request->documentoAbono;
        /// tarel lo que debe para realizar el pago total
        $suma = DB::table('factura')
        ->where('documento', $documento)
        ->sum('debe');
        
        $GananciasD = new GananciasDIarias();
        $GananciasD->Total_G = $suma;
        $GananciasD->documento = $documento; 
        $GananciasD->mp = 4; 
        $GananciasD->save();

        $Abonos = new CuentasPendientes();
        $Abonos->documento = $documento; 
        $Abonos->abono = $suma;
        $Abonos->save();

        $AbonosClientes = DB::table('cuentas_pendientes')
        ->where('documento', $documento)
        ->update(['estado' => 1]);

        $updatedRows = DB::table('factura')
        ->where('documento', $documento)
        ->where('mp', 3)
        ->update(['debe' => 0,'mp' => 4]);

        if ($updatedRows > 0) {
            return response()->json([
                'success' => 'Todas las facturas del cliente han sido abonadas exitosamente.'
            ]);
        }
    }
    /// productos deudas 
    public function listarproductosD(Request $request) {
        $documento = $request->documento;
         
        $Abonos = CuentasPendientes::where('documento', $documento) 
        ->where('estado', 0)
        ->sum('abono');
        $sumaDebe = Factura::where('documento', $documento)
                ->where('mp', 3)
                ->sum('debe');
        $totalDeuda = Factura::where('documento', $documento)
            ->where('mp', 3)
            ->pluck('descripcion');
    
        $itemsArray = $totalDeuda->map(function ($item) {
            $decoded = json_decode($item, true);
            if (is_array($decoded)) {
                return $decoded; 
            }
            return $decoded;
        });
        $itemsArray = $itemsArray->flatten(1);
        return [
            'itemsArray' => $itemsArray,
            'sumaDebe' => $sumaDebe,
            'Abonos' => $Abonos,
        ];
    }
    
    
}
