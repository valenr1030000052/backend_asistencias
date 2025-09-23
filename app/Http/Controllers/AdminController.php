<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Registro;

class AdminController extends Controller
{
    public function panel()
    {
        return view('admin');
    }

    public function listarRegistros(Request $request)
    {
        $query = Registro::with(['usuario', 'sede'])->orderBy('hora_entrada', 'desc');

        if ($request->filled('ciudad')) {
            $query->whereHas('sede', fn($q) => $q->where('ciudad', $request->ciudad));
        }

        if ($request->filled('fecha')) {
            $query->whereDate('hora_entrada', $request->fecha);
        }

        return $query->get()->map(function ($registro) {
            return [
                'id'            => $registro->id,
                'codigo'        => $registro->usuario->codigo_barras ?? '-',
                'usuario_nombre'=> $registro->usuario->nombre ?? 'Desconocido',
                'ciudad'        => $registro->sede?->ciudad,
                'sede_nombre'   => $registro->sede?->nombre,
                'hora_entrada'  => optional($registro->hora_entrada)->format('Y-m-d H:i:s'),
                'hora_salida'   => optional($registro->hora_salida)->format('Y-m-d H:i:s'),
            ];
        });
    }
}