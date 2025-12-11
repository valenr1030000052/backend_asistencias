<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Registro;
use App\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class RegistroController extends Controller
{
    // ✅ Obtener últimos registros (para la tabla)
    public function index()
    {
          $hoy = Carbon::today();

    // Buscar registros del día actual
    $registros = Registro::with(['usuario', 'sede.ciudad'])
        ->whereDate('created_at', $hoy)   // ← CAMBIADO
        ->orderBy('created_at', 'desc')  // ← CAMBIADO
        ->get();

    // Si no hay registros del día → mostrar últimos 20
    if ($registros->count() == 0) {
        $registros = Registro::with(['usuario', 'sede.ciudad'])
            ->orderBy('created_at', 'desc')  // ← CAMBIADO
            ->take(20)
            ->get();
    }

    // Formato JSON
    $registros = $registros->map(function ($r) {

        return [
            'id' => $r->id,

            'usuario' => [
                'codigo_barras' => $r->usuario->codigo_barras ?? '-',
                'nombre'        => $r->usuario->nombre ?? 'Desconocido'
            ],

            'ciudad'      => $r->sede?->ciudad?->nombre ?? '-',
            'sede_nombre' => $r->sede?->nombre ?? '-',

            'hora_entrada' => $r->hora_entrada
                ? Carbon::parse($r->hora_entrada)->format('Y-m-d H:i:s')
                : null,

            'hora_salida'  => $r->hora_salida
                ? Carbon::parse($r->hora_salida)->format('Y-m-d H:i:s')
                : null
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