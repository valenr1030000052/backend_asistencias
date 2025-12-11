<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Registro;
use App\Models\Sede;
use App\Models\Ciudad;
use Carbon\Carbon;

class AdminApiController extends Controller
{
    /**
     * 👉 LISTAR REGISTROS CON FILTROS (CIUDAD + SEDE + BUSCADOR)
     */
    public function registros(Request $request)
    {
        try {

            $q = Registro::with([
                'usuario',
                'sede.ciudad'
            ]);

            // 🔍 Buscador general
            if ($request->filled('buscar')) {
                $b = $request->buscar;

                $q->whereHas('usuario', function($u) use ($b) {
                    $u->where('nombre', 'like', "%$b%")
                      ->orWhere('documento', 'like', "%$b%")
                      ->orWhere('codigo_barras', 'like', "%$b%");
                });
            }

            // 🏙️ Filtro por ciudad
            if ($request->filled('ciudad_id')) {
                $q->whereHas('sede', function($s) use ($request) {
                    $s->where('ciudad_id', $request->ciudad_id);
                });
            }

            // 🏢 Filtro por sede
            if ($request->filled('sede_id')) {
                $q->where('sede_id', $request->sede_id);
            }

            // 📅 Filtros por fecha
            if ($request->filled('fecha_inicio')) {
                $q->whereDate('hora_entrada', '>=', $request->fecha_inicio);
            }

            if ($request->filled('fecha_fin')) {
                $q->whereDate('hora_entrada', '<=', $request->fecha_fin);
            }

            // Orden + paginación
            $results = $q->orderBy('hora_entrada', 'desc')->paginate(200);

            // 🔄 Formato de datos
            $data = $results->getCollection()->map(function ($reg) {
                return [
                    "id"           => $reg->id,
                    "usuario"      => $reg->usuario,
                    "sede"         => $reg->sede,
                    "ciudad"       => $reg->sede?->ciudad,
                    "fecha"        => $reg->hora_entrada ? Carbon::parse($reg->hora_entrada)->format("Y-m-d") : null,
                    "hora_entrada" => $reg->hora_entrada ? Carbon::parse($reg->hora_entrada)->format("H:i:s") : null,
                    "hora_salida"  => $reg->hora_salida  ? Carbon::parse($reg->hora_salida)->format("H:i:s") : null,
                ];
            });

            $results->setCollection(collect($data));

            return response()->json([
                'status' => 'success',
                'data'   => $results
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status'  => 'error',
                'message' => 'Error al listar registros',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ⭐ NUEVO
     * 👉 LISTAR REGISTROS SÓLO DE UNA SEDE (SOLO HOY)
     * usado por el SCANNER
     */
    public function registrosFiltrados(Request $request)
    {
        try {

            $request->validate([
                'sede_id' => 'required|exists:sedes,id'
            ]);

            $sede_id = $request->sede_id;

            $registros = Registro::with('usuario')
                ->where('sede_id', $sede_id)
                ->whereDate('hora_entrada', Carbon::today())
                ->orderBy('id', 'desc')
                ->get();

            return response()->json($registros);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Error al cargar registros filtrados',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 👉 LISTAR CIUDADES
     */
    public function ciudades()
    {
        try {

            $ciudades = Ciudad::orderBy('nombre')->get();

            return response()->json([
                'status' => 'success',
                'data'   => $ciudades
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status'  => 'error',
                'message' => 'Error al cargar ciudades',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 👉 LISTAR SEDES
     */
    public function sedes(Request $request)
    {
        try {

            $query = Sede::with('ciudad');

            if ($request->filled('ciudad_id')) {
                $query->where('ciudad_id', $request->ciudad_id);
            }

            $sedes = $query->orderBy('nombre')->get();

            return response()->json([
                'status' => 'success',
                'data'   => $sedes
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status'  => 'error',
                'message' => 'Error al cargar sedes',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}