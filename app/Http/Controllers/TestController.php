<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    public function test()
    {
        return response()->json([
            'success' => true,
            'message' => '¡Conexión exitosa con Laravel!',
            'data' => [
                'servidor' => 'Laravel',
                'version' => app()->version(),
                'timestamp' => now()->toDateTimeString(),
                'status' => 'API funcionando correctamente'
            ]
        ], 200);
    }

    public function formulario(Request $request)
    {
        $request->validate([
            'nombre' => 'required|min:3',
            'email' => 'required|email',
            'telefono' => 'required|digits:10',
            'mensaje' => 'required|min:10',
            'tipo_usuario' => 'required|in:normal,admin'
        ]);



        return response()->json([
            'success' => true,
            'message' => 'Formulario recibido correctamente',
            'data' => [
                'usuario_autenticado' => $request->user()->name,
                'tipo_usuario_autenticado' => $request->user()->tipo_usuario,
                'tipo_usuario_formulario' => $request->tipo_usuario,
                'datos_formulario' => $request->only(['nombre', 'email', 'telefono', 'mensaje'])
            ]
        ], 200);
    }
}