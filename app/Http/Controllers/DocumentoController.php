<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use App\Models\DocumentoItem;
use App\Models\Item;
use App\Models\Proveedor;
use App\Services\SapService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DocumentoController extends Controller
{
    public function index(Request $request): View
    {
        $query = Documento::with('proveedor')->withCount('items');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('numero', 'like', "%{$search}%")
                  ->orWhere('codigo_tienda', 'like', "%{$search}%")
                  ->orWhere('nombre_tienda', 'like', "%{$search}%")
                  ->orWhereHas('proveedor', fn($p) => $p->where('nombre', 'like', "%{$search}%"));
            });
        }

        if ($estado = $request->input('estado')) {
            $query->where('estado', $estado);
        }

        $documentos = $query->latest()->paginate(15)->withQueryString();

        return view('documentos.index', compact('documentos'));
    }

    public function create(): View
    {
        $proveedores = Proveedor::where('is_active', true)->orderBy('nombre')->get();
        return view('documentos.create', compact('proveedores'));
    }

    public function store(Request $request): RedirectResponse
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

        // Filtrar solo items con cantidad > 0
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
            'created_by'    => auth()->id(),
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

        // Enviar a SAP
        try {
            $sap = app(SapService::class);
            $resultado = $sap->enviarOrdenCompra($documento);

            $mensaje = $resultado['success']
                ? "Documento {$documento->numero} creado y enviado a SAP correctamente."
                : "Documento {$documento->numero} creado, pero SAP respondió con código {$resultado['http_code']}.";

            $documento->update(['estado' => $resultado['success'] ? 'enviado' : 'borrador']);

        } catch (\Exception $e) {
            $mensaje = "Documento {$documento->numero} creado, pero no se pudo enviar a SAP: {$e->getMessage()}";
        }

        return redirect()->route('documentos.show', $documento)->with('success', $mensaje);
    }

    public function show(Documento $documento): View
    {
        $documento->load('proveedor', 'items', 'creador');
        return view('documentos.show', compact('documento'));
    }

    public function updateEstado(Request $request, Documento $documento): RedirectResponse
    {
        $request->validate([
            'estado' => ['required', 'in:borrador,confirmado,enviado'],
        ]);

        $documento->update(['estado' => $request->estado]);

        return back()->with('success', "Estado actualizado a '{$request->estado}'.");
    }

    public function destroy(Documento $documento): RedirectResponse
    {
        abort_if($documento->estado === 'enviado', 403, 'No se puede eliminar un documento enviado.');

        $documento->delete();

        return redirect()->route('documentos.index')
            ->with('success', "Documento {$documento->numero} eliminado.");
    }

    /**
     * Endpoint JSON: items del proveedor para el formulario dinámico.
     */
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
