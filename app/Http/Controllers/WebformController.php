<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use App\Models\DocumentoItem;
use App\Models\Item;
use App\Models\Proveedor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebformController extends Controller
{
    public function show(Request $request): View
    {
        $request->validate([
            'codigo_tienda' => ['required', 'string', 'max:50'],
            'nombre_tienda' => ['required', 'string', 'max:150'],
        ]);

        $proveedores   = Proveedor::where('is_active', true)->orderBy('nombre')->get();
        $codigoTienda  = $request->input('codigo_tienda');
        $nombreTienda  = $request->input('nombre_tienda');

        return view('webform.pedido', compact('proveedores', 'codigoTienda', 'nombreTienda'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fecha'          => ['required', 'date'],
            'proveedor_id'   => ['required', 'exists:proveedores,id'],
            'codigo_tienda'  => ['required', 'string', 'max:50'],
            'nombre_tienda'  => ['required', 'string', 'max:150'],
            'observaciones'  => ['nullable', 'string', 'max:1000'],
            'items'          => ['required', 'array'],
            'items.*.item_id'  => ['required', 'integer'],
            'items.*.cantidad' => ['required', 'numeric', 'min:0'],
        ]);

        $itemsValidos = collect($request->items)
            ->filter(fn($line) => isset($line['cantidad']) && (float)$line['cantidad'] > 0)
            ->values();

        if ($itemsValidos->isEmpty()) {
            return back()->withInput()
                ->withErrors(['items' => 'Debes ingresar cantidad mayor a 0 en al menos un producto.']);
        }

        $documento = Documento::create([
            'fecha'         => $request->fecha,
            'proveedor_id'  => $request->proveedor_id,
            'codigo_tienda' => $request->codigo_tienda,
            'nombre_tienda' => $request->nombre_tienda,
            'observaciones' => $request->observaciones,
            'estado'        => 'borrador',
            'created_by'    => null,
        ]);

        foreach ($itemsValidos as $line) {
            $item = Item::find($line['item_id']);
            if (!$item) continue;

            DocumentoItem::create([
                'documento_id' => $documento->id,
                'item_id'      => $item->ID,
                'codarticulo'  => trim($item->codarticulo),
                'descripcion'  => trim($item->descripcion),
                'unidadmedida' => trim($item->unidadmedida),
                'cantidad'     => (float)$line['cantidad'],
            ]);
        }

        return view('webform.confirmacion', compact('documento'));
    }

    public function itemsProveedor(Proveedor $proveedor): JsonResponse
    {
        $items = $proveedor->items()->map(fn($item) => [
            'id'           => $item->ID,
            'codarticulo'  => trim($item->codarticulo),
            'descripcion'  => trim($item->descripcion),
            'unidadmedida' => trim($item->unidadmedida),
        ])->values();

        return response()->json($items);
    }
}
