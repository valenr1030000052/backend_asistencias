<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Registro;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ScannerController extends Controller
{
    // ✅ Vista del escáner
    public function view($ciudad = 'popayan', $sede = 'principal', Request $request)
    {
        return view('escanear', compact('ciudad', 'sede'));
    }

    // ✅ API para marcar entrada/salida SIN sede
    public function apiScan(Request $request)
    {
        try {

            // Validación
            $validated = $request->validate([
                'codigo_barras' => 'required|string'
            ]);

            $codigo = $validated['codigo_barras'];

            // Buscar usuario por código o documento
            $usuario = Usuario::where('codigo_barras', $codigo)
                                ->orWhere('documento', $codigo)
                                ->first();

            if (!$usuario) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Usuario no encontrado'
                ], 404);
            }

            // Buscar último registro sin salida
            $ultima = Registro::where('usuario_id', $usuario->id)
                            ->whereNull('hora_salida')
                            ->orderBy('hora_entrada', 'desc')
                            ->first();

            $ahora = Carbon::now(config('app.timezone'));

            if (!$ultima) {

                // 👉 Registrar entrada
                $registro = Registro::create([
                    'usuario_id'   => $usuario->id,
                    'hora_entrada' => $ahora
                ]);

                return response()->json([
                    'status'  => 'success',
                    'tipo'    => 'entrada',
                    'usuario' => $usuario->nombre,
                    'codigo'  => $usuario->codigo_barras,
                    'hora'    => $ahora->format('Y-m-d H:i:s'),

                    'registro'=> [
                        'id'           => $registro->id,
                        'hora_entrada' => $registro->hora_entrada->format('Y-m-d H:i:s'),
                        'hora_salida'  => null
                    ]
                ]);
            }

            // 👉 Registrar SALIDA
            $ultima->update(['hora_salida' => $ahora]);

            return response()->json([
                'status'  => 'success',
                'tipo'    => 'salida',
                'usuario' => $usuario->nombre,
                'codigo'  => $usuario->codigo_barras,
                'hora'    => $ahora->format('Y-m-d H:i:s'),

                'registro'=> [
                    'id'           => $ultima->id,
                    'hora_entrada' => $ultima->hora_entrada->format('Y-m-d H:i:s'),
                    'hora_salida'  => $ahora->format('Y-m-d H:i:s')
                ]
            ]);

        } catch (\Exception $e) {

            Log::error("Error en apiScan: ".$e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => 'Ocurrió un error inesperado',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    // ✅ API para obtener todos los registros (tabla)
    public function apiRegistros()
    {
        $registros = Registro::with('usuario')
                    ->orderBy('hora_entrada', 'desc')
                    ->get();

        return response()->json($registros);
    }
}