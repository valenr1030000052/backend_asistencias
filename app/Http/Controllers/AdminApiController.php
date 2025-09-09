<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Registro;


class AdminApiController extends Controller
{
    // ✅ Listar usuarios
    public function usuarios()
    {
        return response()->json([
            'status' => 'success',
            'data' => Usuario::with('sede')->get()
        ]);
    }

    // ✅ Crear usuario (API)
    public function crearUsuario(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string',
            'documento' => 'nullable|string|unique:usuarios,documento',
            'codigo_barras' => 'required|string|unique:usuarios,codigo_barras',
            'sede_id' => 'required|exists:sedes,id'
        ]);

        $usuario = Usuario::create($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Usuario creado correctamente',
            'data' => $usuario
        ], 201);
    }

    // ✅ Listar registros con filtros
    public function registros(Request $request)
    {
        $q = Registro::with(['usuario', 'sede']);

        if ($request->filled('buscar')) {
            $b = $request->buscar;
            $q->whereHas('usuario', function($u) use ($b) {
                $u->where('nombre','like',"%$b%")
                  ->orWhere('documento','like',"%$b%")
                  ->orWhere('codigo_barras','like',"%$b%");
            });
        }

        if ($request->filled('fecha_inicio')) {
            $q->whereDate('hora_entrada','>=',$request->fecha_inicio);
        }
        if ($request->filled('fecha_fin')) {
            $q->whereDate('hora_entrada','<=',$request->fecha_fin);
        }

        $results = $q->orderBy('hora_entrada','desc')->paginate(200);

        return response()->json([
            'status' => 'success',
            'data' => $results
        ]);
    }
}
