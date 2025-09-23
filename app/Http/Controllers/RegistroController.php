<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Registro;
use App\Models\Usuario;
use Carbon\Carbon;

class RegistroController extends Controller
{
    // ✅ Obtener últimos registros (para la tabla)
    public function index()
    {
        $registros = Registro::with(['usuario', 'sede'])
            ->orderBy('hora_entrada', 'desc')
            ->take(20)
            ->get()
            ->map(function ($registro) {
                return [
                    'id'            => $registro->id,
                    'usuario'       => [
                        'codigo_barras' => $registro->usuario->codigo_barras ?? '-',
                        'nombre'        => $registro->usuario->nombre ?? 'Desconocido',
                    ],
                    'ciudad'        => $registro->sede?->ciudad ?? '-',
                    'sede_nombre'   => $registro->sede?->nombre ?? '-',
                    'hora_entrada'  => $registro->hora_entrada ? Carbon::parse($registro->hora_entrada)->format('Y-m-d H:i:s') : null,
                    'hora_salida'   => $registro->hora_salida ? Carbon::parse($registro->hora_salida)->format('Y-m-d H:i:s') : null,
                ];
            });

        return response()->json($registros);
    }

    // ✅ Registrar entrada o salida
    public function store(Request $request)
    {
        $request->validate([
            'codigo_barras' => 'required|string'
        ]);

        $usuario = Usuario::where('codigo_barras', $request->codigo_barras)
            ->orWhere('documento', $request->codigo_barras)
            ->first();

        if (!$usuario) {
            return response()->json([
                'status' => 'error',
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        $ultimoRegistro = Registro::where('usuario_id', $usuario->id)
            ->whereNull('hora_salida')
            ->orderBy('hora_entrada', 'desc')
            ->first();

        $ahora = Carbon::now();

        if (!$ultimoRegistro) {
            // ✅ Registrar ENTRADA
            $registro = Registro::create([
                'usuario_id'   => $usuario->id,
                'sede_id'      => $usuario->sede_id,
                'hora_entrada' => $ahora,
            ]);

            return response()->json([
                'status'        => 'success',
                'tipo'          => 'entrada',
                'message'       => '✅ Entrada registrada',
                'usuario'       => [
                    'codigo_barras' => $usuario->codigo_barras,
                    'nombre'        => $usuario->nombre
                ],
                'hora'          => $ahora->format('Y-m-d H:i:s'),
                'registro_id'   => $registro->id
            ]);
        }

        // ✅ Registrar SALIDA
        $ultimoRegistro->update(['hora_salida' => $ahora]);

        return response()->json([
            'status'        => 'success',
            'tipo'          => 'salida',
            'message'       => '👋 Salida registrada',
            'usuario'       => [
                'codigo_barras' => $usuario->codigo_barras,
                'nombre'        => $usuario->nombre
            ],
            'hora'          => $ahora->format('Y-m-d H:i:s'),
            'registro_id'   => $ultimoRegistro->id
        ]);
    }
}