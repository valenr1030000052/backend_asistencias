<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Registro;

class AdminController extends Controller
{
      // panel admin (GET /admin)
    public function panel()
    {
        if (!session('admin_id')) return redirect()->route('admin.login.form');
        // no cargamos registros aquí (los pedirá el JS por AJAX)
        $usuarios = Usuario::all();
        return view('admin.panel', compact('usuarios'));
    }

    // Crear usuario desde el panel
    public function crearUsuario(Request $request)
    {
        $request->validate([
            'nombre'=>'required',
            'documento'=>'nullable',
            'codigo_barras'=>'required|unique:usuarios,codigo_barras'
        ]);

        Usuario::create([
            'nombre' => $request->nombre,
            'documento' => $request->documento,
            'codigo_barras' => $request->codigo_barras
        ]);

        return redirect()->back()->with('success','Usuario creado');
    }

    // Endpoint AJAX para listar registros con filtros
    public function listarRegistros(Request $request)
    {
        if (!session('admin_id')) return response()->json(['message'=>'No autorizado'],401);

        $q = Registro::with('usuario');

        if ($request->filled('buscar')) {
            $b = $request->buscar;
            $q->whereHas('usuario', function($u) use ($b){
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
        return response()->json($results);
    }
}
