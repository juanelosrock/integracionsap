<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Proveedor;
use App\Models\ProveedorItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProveedorController extends Controller
{
    public function index(Request $request): View
    {
        $query = Proveedor::withCount('proveedorItems');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('codigo_sap', 'like', "%{$search}%")
                  ->orWhere('nit', 'like', "%{$search}%")
                  ->orWhere('ciudad', 'like', "%{$search}%");
            });
        }

        $proveedores = $query->orderBy('nombre')->paginate(15)->withQueryString();

        return view('proveedores.index', compact('proveedores'));
    }

    public function create(): View
    {
        return view('proveedores.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'codigo_sap'        => ['required', 'string', 'max:50', 'unique:proveedores,codigo_sap'],
            'nombre'            => ['required', 'string', 'max:150'],
            'nit'               => ['nullable', 'string', 'max:30'],
            'email'             => ['nullable', 'email', 'max:150'],
            'telefono'          => ['nullable', 'string', 'max:30'],
            'direccion'         => ['nullable', 'string', 'max:255'],
            'ciudad'            => ['nullable', 'string', 'max:100'],
            'pais'              => ['nullable', 'string', 'max:100'],
            'contacto'          => ['nullable', 'string', 'max:100'],
            'cargo_contacto'    => ['nullable', 'string', 'max:100'],
            'telefono_contacto' => ['nullable', 'string', 'max:30'],
            'is_active'         => ['boolean'],
        ]);

        $proveedor = Proveedor::create($data);

        return redirect()->route('proveedores.show', $proveedor)
            ->with('success', "Proveedor '{$proveedor->nombre}' creado correctamente.");
    }

    public function show(Request $request, Proveedor $proveedor): View
    {
        $items          = $proveedor->items();
        $itemIds        = $proveedor->itemIds();
        $searchResults  = collect();
        $searchQuery    = $request->input('item_search');

        if ($searchQuery) {
            $searchResults = Item::where(function ($q) use ($searchQuery) {
                $q->where('codarticulo', 'like', "%{$searchQuery}%")
                  ->orWhere('descripcion', 'like', "%{$searchQuery}%")
                  ->orWhere('refproveedor', 'like', "%{$searchQuery}%");
            })->orderBy('codarticulo')->limit(50)->get();
        }

        return view('proveedores.show', compact('proveedor', 'items', 'itemIds', 'searchResults', 'searchQuery'));
    }

    public function edit(Proveedor $proveedor): View
    {
        return view('proveedores.edit', compact('proveedor'));
    }

    public function update(Request $request, Proveedor $proveedor): RedirectResponse
    {
        $data = $request->validate([
            'codigo_sap'        => ['required', 'string', 'max:50', 'unique:proveedores,codigo_sap,' . $proveedor->id],
            'nombre'            => ['required', 'string', 'max:150'],
            'nit'               => ['nullable', 'string', 'max:30'],
            'email'             => ['nullable', 'email', 'max:150'],
            'telefono'          => ['nullable', 'string', 'max:30'],
            'direccion'         => ['nullable', 'string', 'max:255'],
            'ciudad'            => ['nullable', 'string', 'max:100'],
            'pais'              => ['nullable', 'string', 'max:100'],
            'contacto'          => ['nullable', 'string', 'max:100'],
            'cargo_contacto'    => ['nullable', 'string', 'max:100'],
            'telefono_contacto' => ['nullable', 'string', 'max:30'],
            'is_active'         => ['boolean'],
        ]);

        $proveedor->update($data);

        return redirect()->route('proveedores.show', $proveedor)
            ->with('success', 'Proveedor actualizado correctamente.');
    }

    public function destroy(Proveedor $proveedor): RedirectResponse
    {
        $proveedor->delete();

        return redirect()->route('proveedores.index')
            ->with('success', "Proveedor '{$proveedor->nombre}' eliminado correctamente.");
    }

    public function addItems(Request $request, Proveedor $proveedor): RedirectResponse
    {
        $request->validate([
            'item_ids'   => ['required', 'array', 'min:1'],
            'item_ids.*' => ['integer'],
        ]);

        $existing   = $proveedor->itemIds();
        $nuevos     = array_diff($request->input('item_ids'), $existing);
        $agregados  = 0;

        foreach ($nuevos as $itemId) {
            if (Item::where('ID', $itemId)->exists()) {
                ProveedorItem::create([
                    'proveedor_id' => $proveedor->id,
                    'item_id'      => $itemId,
                ]);
                $agregados++;
            }
        }

        $msg = $agregados > 0
            ? "{$agregados} producto(s) agregado(s) correctamente."
            : 'Los productos seleccionados ya estaban asociados.';

        return back()->withInput(['item_search' => $request->input('item_search_back')])
                     ->with('success', $msg);
    }

    public function removeItem(Proveedor $proveedor, int $itemId): RedirectResponse
    {
        ProveedorItem::where('proveedor_id', $proveedor->id)
            ->where('item_id', $itemId)
            ->delete();

        return back()->with('success', 'Producto eliminado de la lista del proveedor.');
    }
}
