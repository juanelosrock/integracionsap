<?php

namespace App\Http\Controllers;

use App\Models\Serie;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SerieController extends Controller
{
    public function index(Request $request): View
    {
        $query = Serie::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('serie', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%")
                  ->orWhere('empresa_sap', 'like', "%{$search}%")
                  ->orWhere('centro_sap', 'like', "%{$search}%")
                  ->orWhere('nomciudad_sap', 'like', "%{$search}%");
            });
        }

        $series = $query->orderBy('serie')->paginate(20)->withQueryString();

        return view('series.index', compact('series'));
    }

    public function show(Serie $serie): View
    {
        return view('series.show', compact('serie'));
    }
}
