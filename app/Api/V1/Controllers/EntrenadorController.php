<?php

namespace App\Api\V1\Controllers;

use Exception;
use App\Entrenador;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EntrenadorController extends Controller
{
    public function crear(Request $request)
    {
        $nombre = $request->nombre;
        try {
            if (empty($nombre)) {
                throw new Exception("Debe Ingresar el nombre.");
            }
            $entrenador = Entrenador::create([
                'nombre' => $nombre
            ]);
            return [
                'status' => 'ok',
                'id_entrenador' => $entrenador->id
            ];
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function detalle($id)
    {
        $entrenador = null;
        try {
            $entrenador = Entrenador::select(
                'entrenadores.id as id',
                'entrenadores.nombre as nombre',
                'equipos.nombre as equipo'
            )
                ->join('equipos', 'equipos.id_entrenadores', 'entrenadores.id')
                ->where('entrenadores.id', $id)
                ->get()
                ->toArray();


            if (empty($entrenador)) {
                throw new Exception("No se encontro el entrenador.");
            }

            return [
                'status' => 'ok',
                'entrenador' => $entrenador
            ];
        } catch (Exception $e) {
            throw $e;
        }
    }
}
