<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CsvController extends Controller
{
    public function upload(Request $request)
    {
        // Validar que sea CSV
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240', // Max 10MB
        ]);

        try {
            // Guardar el archivo temporalmente
            $file = $request->file('file');
            $fileName = 'temp_' . time() . '.csv';
            $filePath = storage_path('app/temp/' . $fileName);
            
            // Crear directorio si no existe
            if (!file_exists(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0777, true);
            }
            
            // Mover archivo
            $file->move(storage_path('app/temp'), $fileName);

            // Limpiar tabla temporal
            DB::table('datos_temp')->truncate();

            // Convertir path para Windows/Mac (usar / en lugar de \)
            $filePath = str_replace('\\', '/', $filePath);

            // Ejecutar LOAD DATA LOCAL INFILE
            $sql = "
                LOAD DATA LOCAL INFILE '{$filePath}'
                INTO TABLE datos_temp
                FIELDS TERMINATED BY ','
                ENCLOSED BY '\"'
                LINES TERMINATED BY '\n'
                IGNORE 1 ROWS
                (nombre, paterno, materno, telefono, calle, numero_exterior, numero_interior, colonia, cp)
            ";

            DB::connection()->getPdo()->exec($sql);

            // Contar registros cargados
            $countTemp = DB::table('datos_temp')->count();

            // Ejecutar el Stored Procedure
            DB::statement('CALL procesar_carga_masiva()');

            // Contar registros procesados
            $countPersonas = DB::table('persona')->count();
            $countTelefonos = DB::table('telefono')->count();
            $countDirecciones = DB::table('direccion')->count();

            // Eliminar archivo temporal
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            return response()->json([
                'success' => true,
                'message' => "Procesamiento exitoso",
                'datos' => [
                    'registros_cargados' => $countTemp,
                    'personas_creadas' => $countPersonas,
                    'telefonos_registrados' => $countTelefonos,
                    'direcciones_registradas' => $countDirecciones
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el archivo: ' . $e->getMessage()
            ], 500);
        }
    }
}