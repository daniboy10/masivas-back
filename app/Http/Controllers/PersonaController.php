<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PersonaController extends Controller
{
    // Obtener lista de personas con paginación
    public function index(Request $request)
    {
        try {
            $page = $request->input('page', 1);
            $perPage = 100;
            $offset = ($page - 1) * $perPage;

            // Llamar al SP de consulta
            $personas = DB::select('CALL obtener_personas_paginadas(?, ?)', [$offset, $perPage]);
            
            // Contar total de personas
            $total = DB::table('persona')->count();
            
            return response()->json([
                'success' => true,
                'data' => $personas,
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => ceil($total / $perPage)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener personas: ' . $e->getMessage()
            ], 500);
        }
    }

    // Obtener detalle de una persona (teléfonos y direcciones)
    public function show($id)
    {
        try {
            $persona = DB::table('persona')->where('id', $id)->first();
            
            if (!$persona) {
                return response()->json([
                    'success' => false,
                    'message' => 'Persona no encontrada'
                ], 404);
            }

            $telefonos = DB::table('telefono')
                ->where('persona_id', $id)
                ->get();
            
            $direcciones = DB::table('direccion')
                ->where('persona_id', $id)
                ->get();

            return response()->json([
                'success' => true,
                'persona' => $persona,
                'telefonos' => $telefonos,
                'direcciones' => $direcciones
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener detalle: ' . $e->getMessage()
            ], 500);
        }
    }
}