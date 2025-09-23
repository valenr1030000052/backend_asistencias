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
    public function view($ciudad = 'popayan', $sede = 'principal')
    {
        return view('escanear', compact('ciudad', 'sede')); // usa la vista correcta
    }

    // ✅ API para marcar entrada/salida
    public function apiScan(Request $request)
    {
        try {
            // ✅ Validación
            $validated = $request->validate([
                'codigo_barras' => 'required|string',
                'sede_id'       => 'nullable|exists:sedes,id'
            ]);

            $codigo = $validated['codigo_barras'];

            // ✅ Buscar usuario
            $usuario = Usuario::where('codigo_barras', $codigo)
                            ->orWhere('documento', $codigo)
                            ->first();

            if (!$usuario) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Usuario no encontrado'
                ], 404);
            }

            // ✅ Buscar último registro abierto
            $ultima = Registro::where('usuario_id', $usuario->id)
                        ->whereNull('hora_salida')
                        ->orderBy('hora_entrada', 'desc')
                        ->first();

            $ahora = Carbon::now(config('app.timezone'));

            if (!$ultima) {
                // 👉 Registrar entrada
                $registro = Registro::create([
                    'usuario_id'   => $usuario->id,
                    'sede_id'      => $validated['sede_id'] ?? $usuario->sede_id,
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
                    ],
                    'sede'    => $registro->sede?->nombre
                ]);
            }

            // 👉 Registrar salida
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
                ],
                'sede'    => $ultima->sede?->nombre
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Error en la validación',
                'errors'  => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Error en apiScan: ' . $e->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => 'Ocurrió un error inesperado',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}