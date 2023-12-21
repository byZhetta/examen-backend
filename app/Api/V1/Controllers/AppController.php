<?php

namespace App\Api\V1\Controllers;

use App\PokeApi;
use App\Pokemon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;

class AppController extends Controller
{
    public function getPokemones()
    {
        try {
            $cliente = new PokeApi();
            
            $pokemones = $cliente->getUrlApi()->get('pokemon/?limit=15&offset=0.');
            $pokemones = json_decode($pokemones->getBody()->getContents(), true);
            
            $n = 1;
            for ($i = 0; $i <= 14; $i++) {
                
                $tipos = $cliente->getUrlApi()->get('pokemon/'.$n.'');
                $tipos = json_decode($tipos->getBody()->getContents(), true);

                $pokemon = new Pokemon([
                    'id' => $n,
                    'nombre' => $pokemones['results'][$i]['name'], 
                    'tipo' => $tipos['types'][0]['type']['name']
                ]);
                $pokemon->save();
                
                $n++;
            }
            return [
                'status' => 'ok',
                'message' => 'Se guardaron 15 Pokemones con éxito.'
            ];
        } catch (Exception $e) {
            throw $e;
        }
    }
}
