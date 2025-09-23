<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Registro;


class AdminApiController extends Controller
{
    // ✅ Listar usuarios (GET /api/admin/usuarios)
    public function usuarios()
    {
        return response()->json([
            'status' => 'success',
            'data' => Usuario::with('sede')->get()
        ]);
    }

    // ✅ Crear usuario (POST /api/admin/usuarios)
    public function crearUsuario(Request $request)
    {
        try {
            // Validaciones
            $request->validate([
                'nombre' => 'required|string',
                'documento' => 'nullable|string|unique:usuarios,documento',
                'codigo_barras' => 'required|string|unique:usuarios,codigo_barras',
                'sede_id' => 'required|exists:sedes,id'
            ]);

            // Crear usuario
            $usuario = Usuario::create($request->all());

            return response()->json([
                'status' => 'success',
                'message' => 'Usuario creado correctamente',
                'data' => $usuario
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Errores de validación → JSON claro
            return response()->json([
                'status' => 'error',
                'message' => 'Error en la validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // Errores inesperados
            return response()->json([
                'status' => 'error',
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ✅ Listar registros con filtros (GET /api/admin/registros)
    public function registros(Request $request)
    {
        try {
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
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al listar registros',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}