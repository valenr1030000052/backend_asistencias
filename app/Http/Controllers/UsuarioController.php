<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;

class UsuarioController extends Controller
{
    // 📌 Listar todos los usuarios
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'data' => Usuario::all()
        ]);
    }

    // 📌 Crear usuario
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string',
            'documento' => 'required|unique:usuarios',
            'codigo_barras' => 'required|unique:usuarios',
            'sede_id' => 'required|exists:sedes,id',
        ]);

        $usuario = Usuario::create($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Usuario creado correctamente',
            'data' => $usuario
        ], 201);
    }

    // 📌 Mostrar un usuario específico
    public function show(Usuario $usuario)
    {
        return response()->json([
            'status' => 'success',
            'data' => $usuario
        ]);
    }

    // 📌 Actualizar usuario
    public function update(Request $request, Usuario $usuario)
    {
        $request->validate([
            'nombre' => 'sometimes|required|string',
            'documento' => 'sometimes|required|unique:usuarios,documento,' . $usuario->id,
            'codigo_barras' => 'sometimes|required|unique:usuarios,codigo_barras,' . $usuario->id,
            'sede_id' => 'sometimes|required|exists:sedes,id',
        ]);

        $usuario->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Usuario actualizado correctamente',
            'data' => $usuario
        ]);
    }

    // 📌 Eliminar usuario
    public function destroy(Usuario $usuario)
    {
        $usuario->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Usuario eliminado correctamente'
        ]);
    }
}
