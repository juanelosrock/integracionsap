<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ItemController extends Controller
{
    public function index(Request $request): View
    {
        $query = Item::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('codarticulo', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%")
                  ->orWhere('refproveedor', 'like', "%{$search}%")
                  ->orWhere('ref_sap', 'like', "%{$search}%");
            });
        }

        if ($sector = $request->input('sector')) {
            $query->where('sector_sap', $sector);
        }

        $items   = $query->orderBy('codarticulo')->paginate(20)->withQueryString();
        $sectores = Item::distinct()->orderBy('sector_sap')->pluck('sector_sap');

        return view('items.index', compact('items', 'sectores'));
    }

    public function show(Item $item): View
    {
        return view('items.show', compact('item'));
    }
}
