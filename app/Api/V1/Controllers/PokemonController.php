<?php

namespace App\Api\V1\Controllers;

use App\Pokemon;
use App\Http\Controllers\Controller;
use Exception;

class PokemonController extends Controller
{
    public function listar()
    {
        $pokemones = [];
        try {
            $pokemones = Pokemon::all();
            return [
                'status' => 'ok',
                'pokemones' => $pokemones
            ];
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function detalle($id) 
    {
        $pokemon = null;
        try {
            $pokemon = Pokemon::select(
                'id', 'nombre', 'tipo'
            )
                ->where('id', $id)
                ->get()
                ->toArray();

            if (empty($pokemon)) {
                throw new Exception("No se encontro el pokemon.");
            }

            return [
                'status' => 'ok',
                'pokemon' => $pokemon
            ];
        } catch (Exception $e) {
            throw $e;
        }
    }
}
