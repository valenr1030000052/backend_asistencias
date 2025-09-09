<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Registro;

class RegistroController extends Controller
{
    public function index()
    {
        return Registro::with(['usuario', 'sede'])->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'usuario_id' => 'required|exists:usuarios,id',
            'sede_id' => 'required|exists:sedes,id',
            'hora_entrada' => 'nullable|date',
            'hora_salida' => 'nullable|date',
        ]);

        return Registro::create($request->all());
    }

    public function show(Registro $registro)
    {
        return $registro->load(['usuario', 'sede']);
    }

    public function update(Request $request, Registro $registro)
    {
        $registro->update($request->all());
        return $registro;
    }

    public function destroy(Registro $registro)
    {
        $registro->delete();
        return response()->noContent();
    }
}
