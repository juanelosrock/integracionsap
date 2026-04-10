<x-webform-layout titulo="Formulario de Pedido">

<style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { background: #f0f0f0; font-family: 'Calibri', 'Segoe UI', sans-serif; font-size: 13px; }

    /* ── Ribbon ── */
    .xl-ribbon {
        background: #217346;
        color: #fff;
        padding: 7px 12px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 700;
        font-size: 14px;
        flex-wrap: wrap;
    }
    .xl-ribbon-title { flex: 1; min-width: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .xl-ribbon-meta  { font-size: 11px; font-weight: 400; opacity: .85; white-space: nowrap; }
    .xl-ribbon svg   { width: 20px; height: 20px; fill: #fff; flex-shrink: 0; }

    /* ── Barra de fórmula ── */
    .xl-formulabar {
        background: #fff;
        border-bottom: 1px solid #bbb;
        display: flex;
        align-items: center;
        min-height: 26px;
    }
    .xl-namebox {
        width: 90px;
        flex-shrink: 0;
        border-right: 1px solid #bbb;
        padding: 4px 6px;
        font-size: 11px;
        color: #444;
        background: #f5f5f5;
        text-align: center;
        white-space: nowrap;
    }
    .xl-fxlabel {
        padding: 4px 8px;
        font-style: italic;
        color: #666;
        font-size: 12px;
        border-right: 1px solid #ddd;
        flex-shrink: 0;
    }
    .xl-fxvalue {
        padding: 4px 8px;
        font-size: 11px;
        color: #1a1a1a;
        flex: 1;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* ── Contenedor hoja ── */
    .xl-sheet-wrap { overflow-x: auto; background: #fff; -webkit-overflow-scrolling: touch; }

    /* ── Tabla ── */
    .xl-sheet { border-collapse: collapse; width: 100%; min-width: 480px; }

    /* ── Encabezados de columna letras ── */
    .xl-col-header {
        background: #f2f2f2;
        border: 1px solid #c8c8c8;
        text-align: center;
        font-size: 11px;
        color: #555;
        font-weight: 700;
        padding: 3px 4px;
        position: sticky;
        top: 0;
        z-index: 3;
        user-select: none;
    }
    .xl-col-header.rn { width: 36px; }

    /* ── Celdas ── */
    .xl-cell {
        border: 1px solid #d0d0d0;
        padding: 0;
        height: 24px;
        vertical-align: middle;
    }
    .xl-cell.rn {
        background: #f2f2f2;
        text-align: center;
        font-size: 10px;
        color: #777;
        font-weight: 700;
        width: 36px;
        padding: 2px 3px;
        border-color: #c8c8c8;
        position: sticky;
        left: 0;
        z-index: 1;
    }
    .xl-cell.lbl {
        background: #f2f2f2;
        font-weight: 700;
        font-size: 12px;
        color: #333;
        padding: 2px 8px;
        border-color: #c8c8c8;
        white-space: nowrap;
    }
    .xl-cell.sec {
        background: #217346;
        color: #fff;
        font-weight: 700;
        font-size: 12px;
        padding: 4px 8px;
        letter-spacing: .3px;
    }
    .xl-cell.val { background: #fff; }
    .xl-cell.blank { background: #fff; }

    /* Inputs dentro de celdas */
    .xl-cell input[type="text"],
    .xl-cell input[type="date"],
    .xl-cell select,
    .xl-cell textarea {
        width: 100%; height: 100%;
        border: none; outline: none;
        padding: 2px 6px;
        font-family: 'Calibri', 'Segoe UI', sans-serif;
        font-size: 13px;
        background: transparent;
        color: #1a1a1a;
    }
    .xl-cell select { cursor: pointer; }
    .xl-cell textarea { resize: none; height: 44px; padding-top: 5px; line-height: 1.4; }
    .xl-cell input:focus,
    .xl-cell select:focus { background: #e8f0fe; box-shadow: inset 0 0 0 2px #4472c4; }

    /* ── Filas de producto ── */
    .xl-cell.p-even  { background: #ffffff; }
    .xl-cell.p-odd   { background: #f9f9f9; }
    .xl-cell.p-active{ background: #e2efda !important; }
    .xl-cell.p-code  { font-family: 'Courier New', monospace; font-size: 11px; text-align: center; padding: 2px 4px; color: #444; }
    .xl-cell.p-desc  { padding: 2px 6px; font-size: 12px; }
    .xl-cell.p-unit  { text-align: center; font-size: 11px; color: #555; padding: 2px 4px; }
    .xl-cell.p-qty   { background: #fff2cc; text-align: center; }
    .xl-cell.p-qty.has-v { background: #e2efda; }

    .xl-cell.p-qty input[type="number"] {
        width: 100%; height: 100%;
        border: none; outline: none;
        padding: 2px 4px;
        font-family: 'Calibri', monospace;
        font-size: 13px;
        font-weight: 700;
        background: transparent;
        text-align: center;
        color: #1a1a1a;
    }
    .xl-cell.p-qty input:focus { background: #e8f0fe; box-shadow: inset 0 0 0 2px #4472c4; }

    /* ── Totales ── */
    .xl-cell.tot-lbl { background: #dae3f3; font-weight: 700; font-size: 12px; text-align: right; padding: 3px 8px; }
    .xl-cell.tot-val { background: #dae3f3; font-weight: 700; font-family: monospace; text-align: center; padding: 3px 6px; }

    /* ── Barra de estado ── */
    .xl-statusbar {
        background: #217346;
        color: #d4f0e0;
        font-size: 11px;
        padding: 3px 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 4px;
    }

    /* ── Botón enviar ── */
    .xl-btn {
        background: #217346;
        color: #fff;
        border: 1px solid #1a5c38;
        padding: 6px 20px;
        font-size: 13px;
        font-family: 'Calibri', sans-serif;
        font-weight: 700;
        cursor: pointer;
        border-radius: 2px;
        white-space: nowrap;
    }
    .xl-btn:hover { background: #1a5c38; }
    .xl-btn:active { transform: translateY(1px); }

    /* ── Spinner ── */
    .spinner { display:inline-block; width:12px; height:12px; border:2px solid rgba(255,255,255,.4);
               border-top-color:#fff; border-radius:50%; animation:spin .7s linear infinite; vertical-align:middle; }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* ── Errores ── */
    .xl-errors { background:#fce4e4; border:1px solid #e06060; padding:6px 12px; font-size:12px; color:#c00; }

    /* ════════════════════════════════
       RESPONSIVE — Móvil (<600px)
       ════════════════════════════════ */
    @media (max-width: 599px) {
        body { font-size: 12px; }

        .xl-ribbon { font-size: 13px; padding: 6px 10px; }
        .xl-ribbon-meta { display: none; }              /* ocultamos el código en móvil, ya está en fx bar */
        .xl-namebox { width: 70px; font-size: 10px; }
        .xl-fxvalue { font-size: 10px; }

        /* En móvil la tabla puede hacer scroll lateral */
        .xl-sheet { min-width: 340px; }

        /* Ocultamos la columna de letras (A/B/C) en móvil */
        .xl-col-header.letter { display: none; }

        /* La columna de código se fusiona con descripción en móvil
           → usamos display:none en la celda de código y metemos el código dentro de desc */
        .hide-mobile { display: none; }
        .xl-cell.p-info { padding: 3px 6px; }

        .xl-cell { height: 32px; }             /* filas más altas → más fácil de tocar */
        .xl-cell.p-qty input[type="number"] { font-size: 16px; }   /* previene zoom iOS */
        .xl-cell.p-qty { min-width: 60px; }

        /* Campo de cantidad: hacemos la celda más alta en móvil */
        .xl-cell.input-row { height: 34px; }
        .xl-cell textarea  { height: 52px; }
    }

    /* ── Tablet (600-899px) ── */
    @media (min-width: 600px) and (max-width: 899px) {
        .xl-col-header.letter-unit { display: none; }
        .hide-tablet { display: none; }
        .xl-sheet { min-width: 420px; }
    }

    /* scrollbar */
    .xl-sheet-wrap::-webkit-scrollbar { height: 8px; }
    .xl-sheet-wrap::-webkit-scrollbar-track { background: #eee; }
    .xl-sheet-wrap::-webkit-scrollbar-thumb { background: #bbb; border-radius: 2px; }
</style>

<div x-data="webformPedido('{{ route('webform.items-proveedor', ':id') }}')"
     style="max-width:860px; margin:0 auto; box-shadow:0 2px 16px rgba(0,0,0,.18);">

    {{-- ── Ribbon ── --}}
    <div class="xl-ribbon">
        <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 1.5L18.5 9H13V3.5zM6 20V4h5v7h7v9H6z"/></svg>
        <span class="xl-ribbon-title">Formulario de Pedido — {{ $nombreTienda }}</span>
        <span class="xl-ribbon-meta">Código: {{ $codigoTienda }}</span>
    </div>

    {{-- ── Barra de fórmula ── --}}
    <div class="xl-formulabar">
        <div class="xl-namebox">PedidoForm</div>
        <div class="xl-fxlabel">fx</div>
        <div class="xl-fxvalue">
            {{ $nombreTienda }} &nbsp;|&nbsp; Código: <strong>{{ $codigoTienda }}</strong>
        </div>
    </div>

    {{-- ── Errores ── --}}
    @if($errors->any())
        <div class="xl-errors">
            @foreach($errors->all() as $e) ⚠ {{ $e }}<br> @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('webform.store') }}" id="wfForm">
        @csrf
        <input type="hidden" name="codigo_tienda" value="{{ $codigoTienda }}">
        <input type="hidden" name="nombre_tienda" value="{{ $nombreTienda }}">

        <div class="xl-sheet-wrap">
        <table class="xl-sheet">

            {{-- ── Encabezados de columna ── --}}
            <thead>
                <tr>
                    <th class="xl-col-header rn"></th>
                    <th class="xl-col-header letter" style="width:100px;">A</th>
                    <th class="xl-col-header letter">B</th>
                    <th class="xl-col-header letter letter-unit" style="width:80px;">C</th>
                    <th class="xl-col-header letter" style="width:90px;">D</th>
                </tr>
            </thead>

            <tbody>

                {{-- Fila 1: Cabecera --}}
                <tr>
                    <td class="xl-cell rn">1</td>
                    <td class="xl-cell sec" colspan="4">CABECERA DEL PEDIDO</td>
                </tr>

                {{-- Fila 2: Fecha --}}
                <tr>
                    <td class="xl-cell rn">2</td>
                    <td class="xl-cell lbl">Fecha</td>
                    <td class="xl-cell input-row" colspan="3">
                        <input type="date" name="fecha" value="{{ old('fecha', date('Y-m-d')) }}" required>
                    </td>
                </tr>

                {{-- Fila 3: Proveedor --}}
                <tr>
                    <td class="xl-cell rn">3</td>
                    <td class="xl-cell lbl">Proveedor</td>
                    <td class="xl-cell input-row" colspan="3">
                        <select name="proveedor_id" required @change="cargarItems($event.target.value)">
                            <option value="">— Seleccionar proveedor —</option>
                            @foreach($proveedores as $p)
                                <option value="{{ $p->id }}" {{ old('proveedor_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->nombre }} ({{ $p->codigo_sap }})
                                </option>
                            @endforeach
                        </select>
                    </td>
                </tr>

                {{-- Fila 4: Observaciones --}}
                <tr style="height:52px;">
                    <td class="xl-cell rn">4</td>
                    <td class="xl-cell lbl" style="vertical-align:top; padding-top:5px;">Obs.</td>
                    <td class="xl-cell" colspan="3" style="height:52px;">
                        <textarea name="observaciones" placeholder="Notas del pedido...">{{ old('observaciones') }}</textarea>
                    </td>
                </tr>

                {{-- Fila 5: Espacio --}}
                <tr><td class="xl-cell rn">5</td><td class="xl-cell blank" colspan="4"></td></tr>

                {{-- Fila 6: Sección productos --}}
                <tr>
                    <td class="xl-cell rn">6</td>
                    <td class="xl-cell sec" colspan="4">
                        DETALLE DE PRODUCTOS
                        <span x-show="cargando" style="margin-left:8px;">
                            <span class="spinner"></span>
                        </span>
                        <span x-show="!cargando && items.length > 0" x-cloak
                              style="font-weight:400; font-size:11px; margin-left:8px; opacity:.8;"
                              x-text="`${items.length} productos`"></span>
                    </td>
                </tr>

                {{-- Fila 7: Encabezados columnas productos --}}
                <tr>
                    <td class="xl-cell rn">7</td>
                    <td class="xl-cell lbl hide-mobile" style="text-align:center; font-size:11px;">CÓDIGO</td>
                    <td class="xl-cell lbl" style="font-size:11px;">DESCRIPCIÓN</td>
                    <td class="xl-cell lbl hide-mobile hide-tablet" style="text-align:center; font-size:11px;">UNIDAD</td>
                    <td class="xl-cell lbl" style="text-align:center; font-size:11px; background:#fff2cc;">CANTIDAD</td>
                </tr>

                {{-- Sin proveedor --}}
                <tr x-show="!proveedorSeleccionado && !cargando" x-cloak>
                    <td class="xl-cell rn">8</td>
                    <td class="xl-cell" colspan="4"
                        style="color:#999; font-style:italic; text-align:center; padding:12px 8px; font-size:12px;">
                        ← Selecciona un proveedor en la fila 3
                    </td>
                </tr>

                {{-- Sin productos --}}
                <tr x-show="proveedorSeleccionado && !cargando && items.length === 0" x-cloak>
                    <td class="xl-cell rn">8</td>
                    <td class="xl-cell" colspan="4"
                        style="color:#b8860b; font-style:italic; text-align:center; padding:12px 8px; font-size:12px;">
                        ⚠ Este proveedor no tiene productos asociados
                    </td>
                </tr>

                {{-- Filas de productos --}}
                <template x-for="(item, index) in items" :key="item.id">
                    <tr>
                        <td class="xl-cell rn" x-text="8 + index"></td>

                        {{-- Código (oculto en móvil) --}}
                        <td class="xl-cell p-code hide-mobile"
                            :class="item.cantidad > 0 ? 'p-active' : (index%2===0 ? 'p-even':'p-odd')"
                            x-text="item.codarticulo">
                        </td>

                        {{-- Descripción (en móvil incluye código) --}}
                        <td class="xl-cell p-desc"
                            :class="item.cantidad > 0 ? 'p-active' : (index%2===0 ? 'p-even':'p-odd')">
                            <span x-text="item.descripcion"></span>
                            {{-- En móvil mostramos el código debajo de la descripción --}}
                            <span class="hide-desktop"
                                  style="display:none; font-size:10px; color:#888; font-family:monospace;"
                                  x-text="item.codarticulo + ' · ' + item.unidadmedida"></span>
                        </td>

                        {{-- Unidad (oculta en tablet y móvil) --}}
                        <td class="xl-cell p-unit hide-mobile hide-tablet"
                            :class="item.cantidad > 0 ? 'p-active' : (index%2===0 ? 'p-even':'p-odd')"
                            x-text="item.unidadmedida">
                        </td>

                        {{-- Cantidad --}}
                        <td class="xl-cell p-qty" :class="item.cantidad > 0 ? 'has-v' : ''">
                            <input type="hidden" :name="`items[${index}][item_id]`" :value="item.id">
                            <input type="number"
                                   :name="`items[${index}][cantidad]`"
                                   x-model.number="item.cantidad"
                                   min="0" step="1" placeholder="0"
                                   inputmode="numeric">
                        </td>
                    </tr>
                </template>

                {{-- Fila totales --}}
                <tr x-show="!cargando && items.length > 0" x-cloak>
                    <td class="xl-cell rn"></td>
                    <td class="xl-cell tot-lbl" colspan="2">TOTAL</td>
                    <td class="xl-cell tot-val hide-mobile hide-tablet" x-text="totalItems() + ' prod.'"></td>
                    <td class="xl-cell tot-val" x-text="totalUnidades() + ' uds.'"></td>
                </tr>

                {{-- Espacio --}}
                <tr><td class="xl-cell rn"></td><td class="xl-cell blank" colspan="4"></td></tr>

                {{-- Botón --}}
                <tr>
                    <td class="xl-cell rn"></td>
                    <td class="xl-cell" colspan="4"
                        style="padding:8px 10px; background:#f5f5f5; border-top:2px solid #217346;">
                        <div style="display:flex; align-items:center; flex-wrap:wrap; gap:10px;">
                            <button type="submit" class="xl-btn" id="btnEnviar">
                                ✔ Enviar Pedido
                            </button>
                            <span x-show="totalItems() > 0" x-cloak
                                  style="font-size:12px; color:#217346; font-weight:600;"
                                  x-text="`${totalItems()} producto(s) · ${totalUnidades()} unidad(es)`">
                            </span>
                            <span x-show="totalItems() === 0 && proveedorSeleccionado && !cargando && items.length > 0" x-cloak
                                  style="font-size:11px; color:#999;">
                                Ingresa cantidades para continuar
                            </span>
                        </div>
                    </td>
                </tr>

            </tbody>
        </table>
        </div>

        {{-- ── Barra de estado ── --}}
        <div class="xl-statusbar">
            <span>Listo</span>
            <span x-show="totalItems() > 0" x-cloak
                  x-text="`Suma: ${totalUnidades()}   Recuento: ${totalItems()}`"></span>
            <span x-show="totalItems() === 0">Sin selección</span>
        </div>

    </form>
</div>

<script>
/* Mostrar código+unidad dentro de descripción en móvil */
(function () {
    function applyMobile() {
        const isMobile = window.innerWidth < 600;
        document.querySelectorAll('.hide-desktop').forEach(el => {
            el.style.display = isMobile ? 'block' : 'none';
        });
    }
    window.addEventListener('resize', applyMobile);
    document.addEventListener('DOMContentLoaded', applyMobile);
})();

/* Deshabilitar inputs con cantidad 0 antes de enviar */
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('wfForm').addEventListener('submit', function () {
        document.querySelectorAll('input[type="number"][name*="[cantidad]"]').forEach(function (input) {
            if (!parseFloat(input.value) || parseFloat(input.value) <= 0) {
                input.disabled = true;
                const hidden = input.closest('td')?.querySelector('input[type="hidden"]');
                if (hidden) hidden.disabled = true;
            }
        });
    });
});

function webformPedido(urlTemplate) {
    return {
        items: [],
        cargando: false,
        proveedorSeleccionado: false,

        cargarItems(proveedorId) {
            this.items = [];
            this.proveedorSeleccionado = false;
            if (!proveedorId) return;
            this.proveedorSeleccionado = true;
            this.cargando = true;

            fetch(urlTemplate.replace(':id', proveedorId), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                this.items = data.map(item => ({ ...item, cantidad: 0 }));
                this.cargando = false;
            })
            .catch(() => { this.cargando = false; });
        },

        totalItems()    { return this.items.filter(i => i.cantidad > 0).length; },
        totalUnidades() { return this.items.reduce((s, i) => s + (i.cantidad > 0 ? Number(i.cantidad) : 0), 0); }
    };
}
</script>

</x-webform-layout>
