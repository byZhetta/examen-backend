<?php

namespace App\Api\V1\Controllers;

use Exception;
use App\Equipo;
use App\EquipoPokemon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class EquipoController extends Controller
{
    public function listar(Request $request)
    {
        try {
            $request->validate(['id_entrenador' => 'required|exists:entrenadores,id']);
            $idEntrenador = $request->id_entrenador;
            $equipos = Equipo::where('id_entrenadores', $idEntrenador)->paginate(10);
            return [
                'status' => 'ok',
                'equipos' => $equipos
            ];
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function detalle($id)
    {
        $equipo = null;
        try {
            $equipo = Equipo::select(
                'equipos.id as id',
                'equipos.nombre as nombre',
                'pokemones.id as id_pokemones',
                'pokemones.nombre as nombre_pokemones',
                'pokemones.tipo as tipos_pokemones',
                'equipos_pokemones.orden as orden_pokemones'
            )
                ->join('equipos_pokemones', 'equipos_pokemones.id_equipos', 'equipos.id')
                ->join('pokemones', 'pokemones.id', 'equipos_pokemones.id_pokemones')
                ->where('equipos.id', $id)
                ->get()
                ->toArray();


            if (empty($equipo)) {
                throw new Exception("No se encontro el equipo.");
            }

            return [
                'status' => 'ok',
                'equipo' => $equipo
            ];
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function crear(Request $request)
    {
        try {
            $request->validate([
                'id_entrenador' => 'required|exists:entrenadores,id',
                'nombre' => 'required'
            ]);

            $equipo = new Equipo([
                'id_entrenadores' => $request->id_entrenador,
                'nombre' => $request->nombre
            ]);
            $equipo->save();

            $eq_creado = DB::table('equipos')
                ->max('id');
            $search = DB::table('equipos_pokemones')
                ->select('id_pokemones')
                ->groupBy('id_pokemones')
                ->get();

            if (count($search)) {
                $poke_array = array();
                foreach ($search as $search) {
                    $poke_array[] = $search->id_pokemones;
                }   
                $pokemones = DB::table('pokemones')
                    ->select('id')
                    ->whereNotIn('id', $poke_array)
                    ->limit(3)
                    ->get();
            } else {
                $pokemones = DB::table('pokemones')
                    ->select('id')
                    ->orderBy('id', 'asc')
                    ->limit(3)
                    ->get();     
            }

            $i = 1;
            foreach ($pokemones as $pokemon) {
                $equipo_pokemon = new EquipoPokemon([
                    'id_equipos' => $eq_creado,
                    'id_pokemones' => $pokemon->id,
                    'orden' => $i
                ]);
                $equipo_pokemon->save();
                $i++;
            }

            return [
                'status' => 'ok',
                'message' => 'Equipo creado exitosamente!.'
            ];
        } catch (Exception $e) {
            throw $e;
        }
    }
}
