<x-webform-layout titulo="Pedido Enviado">

<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { background: #f0f0f0; font-family: 'Calibri', 'Segoe UI', sans-serif; font-size: 13px; }
    .xl-ribbon { background: #217346; color: white; padding: 6px 12px; display: flex;
                 align-items: center; gap: 10px; font-weight: 600; font-size: 14px; }
    .xl-cell { border: 1px solid #d0d0d0; padding: 2px 8px; height: 22px; vertical-align: middle; }
    .xl-cell.label  { background: #f2f2f2; font-weight: 600; color: #333; font-size: 12px; width: 180px; border-color: #c8c8c8; }
    .xl-cell.value  { background: #fff; color: #1a1a1a; }
    .xl-cell.row-num { background: #f2f2f2; text-align: center; font-size: 11px; color: #666;
                       font-weight: 600; width: 42px; border-color: #c8c8c8; }
    .xl-cell.header-section { background: #217346; color: white; font-weight: 700;
                               padding: 3px 8px; font-size: 12px; }
    .xl-cell.success-row { background: #e2efda; font-weight: 700; color: #217346; text-align: center; }
    .xl-col-header { background: #f2f2f2; border: 1px solid #c8c8c8; text-align: center;
                     font-size: 11px; color: #444; font-weight: 600; padding: 3px 4px; }
    .xl-statusbar { background: #217346; color: #d4f0e0; font-size: 11px;
                    padding: 3px 10px; display: flex; justify-content: space-between; }
</style>

<div style="max-width:600px; margin:0 auto; box-shadow: 0 2px 12px rgba(0,0,0,0.18);">

    <div class="xl-ribbon">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="white">
            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
        </svg>
        Pedido Registrado — {{ $documento->numero }}
    </div>

    <div style="background:#fff; border-bottom:1px solid #bbb; padding:4px 10px; font-size:12px; color:#666;">
        fx &nbsp; =CONFIRMAR_PEDIDO("{{ $documento->numero }}")
    </div>

    <div style="overflow-x:auto; background:#fff;">
    <table style="border-collapse:collapse; width:100%;">
        <thead>
            <tr>
                <th class="xl-col-header" style="width:42px;"></th>
                <th class="xl-col-header" style="width:180px;">A</th>
                <th class="xl-col-header">B</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="xl-cell row-num">1</td>
                <td class="xl-cell success-row" colspan="2">
                    ✔ &nbsp; PEDIDO REGISTRADO CORRECTAMENTE
                </td>
            </tr>
            <tr>
                <td class="xl-cell row-num">2</td>
                <td class="xl-cell" colspan="2"
                    style="background:{{ $sapExito ? '#e2efda' : '#fff2cc' }};
                           color:{{ $sapExito ? '#217346' : '#7d6608' }};
                           font-weight:600; font-size:12px; padding:4px 8px;">
                    @if($sapExito)
                        ✔ &nbsp; Orden de compra enviada a SAP exitosamente
                    @else
                        ⚠ &nbsp; Pedido guardado — pendiente de envío a SAP
                        @if($sapMensaje)
                            <span style="font-weight:400; font-size:11px; display:block; margin-top:2px;">
                                {{ $sapMensaje }}
                            </span>
                        @endif
                    @endif
                </td>
            </tr>
            <tr><td class="xl-cell row-num">2</td><td class="xl-cell" colspan="2"></td></tr>
            <tr>
                <td class="xl-cell row-num">3</td>
                <td class="xl-cell header-section" colspan="2">RESUMEN</td>
            </tr>
            <tr>
                <td class="xl-cell row-num">4</td>
                <td class="xl-cell label">Número de Pedido</td>
                <td class="xl-cell value" style="font-family:monospace; font-weight:700; color:#217346;">
                    {{ $documento->numero }}
                </td>
            </tr>
            <tr>
                <td class="xl-cell row-num">5</td>
                <td class="xl-cell label">Fecha</td>
                <td class="xl-cell value">{{ $documento->fecha->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td class="xl-cell row-num">6</td>
                <td class="xl-cell label">Tienda</td>
                <td class="xl-cell value">{{ $documento->nombre_tienda }}
                    <span style="color:#888; font-size:11px;"> ({{ $documento->codigo_tienda }})</span>
                </td>
            </tr>
            <tr>
                <td class="xl-cell row-num">7</td>
                <td class="xl-cell label">Proveedor</td>
                <td class="xl-cell value">{{ $documento->proveedor->nombre }}</td>
            </tr>
            <tr>
                <td class="xl-cell row-num">8</td>
                <td class="xl-cell label">Productos solicitados</td>
                <td class="xl-cell value" style="font-weight:700;">{{ $documento->items->count() }}</td>
            </tr>
            <tr>
                <td class="xl-cell row-num">9</td>
                <td class="xl-cell label">Total unidades</td>
                <td class="xl-cell value" style="font-weight:700;">
                    {{ $documento->items->sum('cantidad') }}
                </td>
            </tr>
            <tr><td class="xl-cell row-num">10</td><td class="xl-cell" colspan="2"></td></tr>
            <tr>
                <td class="xl-cell row-num">11</td>
                <td class="xl-cell" colspan="2"
                    style="color:#888; font-style:italic; font-size:12px; padding:6px 8px;">
                    Tu pedido fue registrado. Puedes cerrar esta ventana.
                </td>
            </tr>
        </tbody>
    </table>
    </div>

    <div class="xl-statusbar">
        <span>Listo</span>
        <span>Suma: {{ $documento->items->sum('cantidad') }}   Recuento: {{ $documento->items->count() }}</span>
    </div>

</div>

</x-webform-layout>
