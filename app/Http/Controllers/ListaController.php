<?php

namespace App\Http\Controllers;

use App\Models\Acta;
use App\Models\Lista;
use App\Models\ListaGrupo;
use Illuminate\Http\Request;

class ListaController extends Controller
{
    public function __construct()
    {
    }

    public function getAll(Request $request)
    {
        $page = $request->has('page') ? $request->get('page') : 1;
        $limit = $request->has('limit') ? $request->get('limit') : 99999999999999;
        $search = $request->has('search') ? $request->get('search') : '';


        $listaGrupos = ListaGrupo::where('codigo', 'LIKE', '%' . $search . '%')
            ->limit($limit)
            ->offset(($page - 1) * $limit)
            ->get();

        foreach ($listaGrupos as $listaGrupo) {
            $idListaGrupo = $listaGrupo->id;
            $listas = Lista::find($idListaGrupo);
            $listaGrupo->listas =  $listas;
        }

        $countActas = ListaGrupo::where('codigo', 'LIKE', '%' . $search . '%')
            ->count();

        return response()->json([
            "data" => $listaGrupos,
            "count" => $countActas,
            "code" => 20000
        ]);

    }

    public function getOne(Request $request)
    {
        $codigoListaGrupo = $request->has('codigo') ? $request->get('codigo') : 0;
        $listaGrupo = ListaGrupo::where('codigo',$codigoListaGrupo)->first();
        $idListaGrupo = $listaGrupo->id;
        $listas = Lista::where('idListaGrupo',$idListaGrupo)->get();
        return response()->json([
            "data" => $listas,
            "code" => 20000
        ]);
    }
}