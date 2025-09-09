<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Registro;
use Carbon\Carbon;

class ScannerController extends Controller
{

    
     // Vista del escáner
    public function view($ciudad = 'popayan', $sede = 'principal')
    {
        return view('scanner', compact('ciudad', 'sede'));
    }

    // API para marcar entrada/salida
    public function apiScan(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string'
        ]);

        $codigo = $request->input('codigo');

        $usuario = Usuario::where('codigo_barras', $codigo)
                    ->orWhere('documento', $codigo)
                    ->first();

        if (!$usuario) {
            return response()->json(['success' => false, 'message' => 'Usuario no encontrado'], 404);
        }

        $ultima = Registro::where('usuario_id', $usuario->id)
                    ->whereNull('hora_salida')
                    ->orderBy('hora_entrada', 'desc')
                    ->first();

        if (!$ultima) {
            $registro = Registro::create([
                'usuario_id' => $usuario->id,
                'sede_id' => $request->input('sede_id') ?? $usuario->sede_id,
                'hora_entrada' => Carbon::now()
            ]);

            return response()->json([
                'success' => true,
                'tipo' => 'entrada',
                'usuario' => $usuario->nombre,
                'hora' => $registro->hora_entrada?->toDateTimeString(),
                'sede' => $registro->sede?->nombre
            ]);
        } else {
            $ultima->hora_salida = Carbon::now();
            $ultima->save();

            return response()->json([
                'success' => true,
                'tipo' => 'salida',
                'usuario' => $usuario->nombre,
                'hora' => $ultima->hora_salida?->toDateTimeString(),
                'sede' => $ultima->sede?->nombre
            ]);
        }
    }
}
