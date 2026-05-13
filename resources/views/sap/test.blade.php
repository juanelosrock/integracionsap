<x-app-layout>
<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        SAP API — Tester
    </h2>
</x-slot>

<style>
    /* ── Layout ── */
    .tester-wrap { display:flex; gap:0; height:calc(100vh - 120px); overflow:hidden; }

    /* ── Panel izquierdo: lista de documentos ── */
    .panel-docs {
        width: 280px; min-width:220px; max-width:320px;
        border-right:1px solid #e5e7eb;
        display:flex; flex-direction:column;
        background:#f9fafb;
        resize:horizontal; overflow:auto;
    }
    .panel-docs-header {
        padding:10px 12px; font-size:11px; font-weight:700;
        text-transform:uppercase; letter-spacing:.06em;
        color:#6b7280; border-bottom:1px solid #e5e7eb;
        background:#f3f4f6;
    }
    .doc-search {
        padding:8px 10px; border-bottom:1px solid #e5e7eb;
    }
    .doc-search input {
        width:100%; padding:5px 8px; font-size:12px;
        border:1px solid #d1d5db; border-radius:4px;
        outline:none;
    }
    .doc-search input:focus { border-color:#3b82f6; }
    .doc-list { overflow-y:auto; flex:1; }
    .doc-item {
        padding:8px 12px; cursor:pointer; border-bottom:1px solid #f0f0f0;
        font-size:12px; transition:background .1s;
    }
    .doc-item:hover { background:#eff6ff; }
    .doc-item.active { background:#dbeafe; border-left:3px solid #3b82f6; }
    .doc-item .doc-num { font-weight:700; color:#1d4ed8; font-size:12px; }
    .doc-item .doc-meta { color:#6b7280; font-size:11px; margin-top:1px; }
    .badge {
        display:inline-block; padding:1px 6px; border-radius:9px;
        font-size:10px; font-weight:600;
    }
    .badge-borrador  { background:#fef3c7; color:#92400e; }
    .badge-confirmado{ background:#dbeafe; color:#1e40af; }
    .badge-enviado   { background:#d1fae5; color:#065f46; }

    /* ── Panel central: editor JSON ── */
    .panel-editor {
        flex:1; display:flex; flex-direction:column; min-width:0;
        border-right:1px solid #e5e7eb;
    }
    .panel-editor-header {
        display:flex; align-items:center; justify-content:space-between;
        padding:8px 14px; background:#1e293b; color:#e2e8f0;
        font-size:12px; font-family:monospace; gap:8px;
    }
    .panel-editor-header .method-badge {
        background:#22c55e; color:#fff; padding:2px 8px;
        border-radius:3px; font-weight:700; font-size:11px;
    }
    .panel-editor-header .url-text {
        flex:1; overflow:hidden; text-overflow:ellipsis;
        white-space:nowrap; color:#94a3b8; font-size:11px;
    }
    .toolbar {
        display:flex; align-items:center; gap:6px;
        padding:6px 12px; border-bottom:1px solid #e5e7eb;
        background:#f8fafc;
    }
    .btn {
        padding:5px 12px; font-size:12px; border-radius:4px;
        border:none; cursor:pointer; font-weight:600; transition:all .15s;
    }
    .btn-send {
        background:#2563eb; color:#fff;
    }
    .btn-send:hover { background:#1d4ed8; }
    .btn-send:disabled { background:#93c5fd; cursor:not-allowed; }
    .btn-secondary {
        background:#f1f5f9; color:#374151; border:1px solid #d1d5db;
    }
    .btn-secondary:hover { background:#e2e8f0; }
    .btn-danger {
        background:#fee2e2; color:#dc2626; border:1px solid #fca5a5;
    }
    .btn-danger:hover { background:#fecaca; }
    .editor-area {
        flex:1; padding:0; overflow:hidden;
    }
    #json-editor {
        width:100%; height:100%; resize:none; border:none; outline:none;
        font-family:'Cascadia Code','Fira Code','Consolas',monospace;
        font-size:12px; line-height:1.6; padding:14px;
        background:#0f172a; color:#e2e8f0;
        tab-size:2;
    }
    .editor-status {
        padding:4px 12px; font-size:11px; background:#1e293b;
        color:#64748b; font-family:monospace;
        display:flex; justify-content:space-between;
    }
    #json-error { color:#f87171; font-size:11px; }

    /* ── Panel derecho: respuesta ── */
    .panel-response {
        width:380px; min-width:260px; max-width:50%;
        display:flex; flex-direction:column;
        resize:horizontal; overflow:auto;
    }
    .response-header {
        display:flex; align-items:center; gap:8px;
        padding:8px 14px; background:#1e293b; color:#e2e8f0;
        font-size:12px; font-family:monospace;
    }
    .status-pill {
        padding:2px 10px; border-radius:10px; font-weight:700;
        font-size:11px;
    }
    .status-2xx { background:#166534; color:#bbf7d0; }
    .status-4xx { background:#7f1d1d; color:#fecaca; }
    .status-5xx { background:#78350f; color:#fed7aa; }
    .status-0   { background:#374151; color:#d1d5db; }
    .response-tabs {
        display:flex; border-bottom:1px solid #e5e7eb;
        background:#f8fafc;
    }
    .response-tab {
        padding:6px 14px; font-size:11px; font-weight:600;
        cursor:pointer; border-bottom:2px solid transparent;
        color:#6b7280; transition:all .1s;
    }
    .response-tab.active { color:#2563eb; border-bottom-color:#2563eb; }
    .response-body {
        flex:1; overflow-y:auto; padding:14px;
        font-family:'Cascadia Code','Fira Code','Consolas',monospace;
        font-size:12px; line-height:1.6; background:#0f172a; color:#e2e8f0;
        white-space:pre-wrap; word-break:break-all;
    }
    .response-body.empty {
        display:flex; align-items:center; justify-content:center;
        color:#475569; font-family:sans-serif; font-size:13px;
    }
    .spinner {
        display:none; width:16px; height:16px;
        border:2px solid #475569; border-top-color:#60a5fa;
        border-radius:50%; animation:spin .7s linear infinite;
    }
    @keyframes spin { to { transform:rotate(360deg); } }

    /* Syntax highlight simple */
    .json-key    { color:#7dd3fc; }
    .json-str    { color:#86efac; }
    .json-num    { color:#fb923c; }
    .json-bool   { color:#c084fc; }
    .json-null   { color:#94a3b8; }

    /* ── Panel Gmail ── */
    .gmail-panel {
        width: 300px; min-width:240px; max-width:360px;
        border-left:1px solid #e5e7eb;
        display:flex; flex-direction:column;
        background:#f9fafb;
    }
    .gmail-panel-header {
        padding:10px 12px; font-size:11px; font-weight:700;
        text-transform:uppercase; letter-spacing:.06em;
        color:#6b7280; border-bottom:1px solid #e5e7eb;
        background:#f3f4f6; display:flex; align-items:center; gap:6px;
    }
    .gmail-search-box {
        padding:10px; border-bottom:1px solid #e5e7eb;
        display:flex; gap:6px;
    }
    .gmail-search-box input {
        flex:1; padding:5px 8px; font-size:12px;
        border:1px solid #d1d5db; border-radius:4px; outline:none;
    }
    .gmail-search-box input:focus { border-color:#3b82f6; }
    .gmail-search-box button {
        padding:5px 10px; font-size:12px; background:#4f46e5;
        color:#fff; border:none; border-radius:4px; cursor:pointer;
        font-weight:600;
    }
    .gmail-search-box button:hover { background:#4338ca; }
    .gmail-search-box button:disabled { background:#a5b4fc; cursor:not-allowed; }
    .gmail-results { flex:1; overflow-y:auto; }
    .gmail-empty {
        padding:24px 12px; text-align:center; color:#9ca3af; font-size:12px;
    }
    .gmail-item {
        padding:9px 12px; border-bottom:1px solid #f0f0f0; font-size:12px;
    }
    .gmail-item.unread { background:#eff6ff; }
    .gmail-item-subject { font-weight:600; color:#1e293b; line-height:1.3; }
    .gmail-item-from { color:#6b7280; font-size:11px; margin-top:2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .gmail-item-date { color:#9ca3af; font-size:10px; margin-top:1px; }
    .gmail-item-snippet { color:#64748b; font-size:11px; margin-top:3px; line-height:1.4; }
    .gmail-badge-new { display:inline-block; width:6px; height:6px; background:#3b82f6; border-radius:50%; margin-right:4px; vertical-align:middle; }
    .gmail-status { padding:6px 12px; font-size:11px; color:#6b7280; border-bottom:1px solid #e5e7eb; background:#f8fafc; }
</style>

<div style="padding:0;">
<div class="tester-wrap">

    <!-- ── Panel izquierdo: documentos ── -->
    <div class="panel-docs">
        <div class="panel-docs-header">Documentos</div>
        <div class="doc-search">
            <input type="text" id="doc-search" placeholder="Buscar número, proveedor...">
        </div>
        <div class="doc-list" id="doc-list">
            @foreach($documentos as $doc)
            <div class="doc-item"
                 data-id="{{ $doc['id'] }}"
                 data-num="{{ $doc['numero'] }}"
                 onclick="seleccionarDoc({{ $doc['id'] }})">
                <div class="doc-num">{{ $doc['numero'] }}</div>
                <div class="doc-meta">
                    {{ $doc['proveedor'] ?? '—' }}<br>
                    {{ $doc['codigo_tienda'] }} · {{ $doc['fecha'] }}
                </div>
                <div style="margin-top:3px;">
                    <span class="badge badge-{{ $doc['estado'] }}">{{ $doc['estado'] }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- ── Panel central: editor ── -->
    <div class="panel-editor">
        <div class="panel-editor-header">
            <span class="method-badge">POST</span>
            <span class="url-text" title="{{ config('sap.url') }}">{{ config('sap.url') }}</span>
        </div>
        <div class="toolbar">
            <button class="btn btn-send" id="btn-send" onclick="enviarRequest()" disabled>
                ▶ Enviar
            </button>
            <button class="btn btn-secondary" onclick="formatearJson()">
                { } Formatear
            </button>
            <button class="btn btn-secondary" onclick="copiarJson()">
                ⎘ Copiar
            </button>
            <button class="btn btn-danger" onclick="limpiarEditor()" title="Limpiar">
                ✕
            </button>
            <span id="doc-label" style="margin-left:8px; font-size:12px; color:#6b7280; font-style:italic;">
                Selecciona un documento →
            </span>
        </div>
        <div class="editor-area">
            <textarea id="json-editor"
                placeholder="← Selecciona un documento para cargar el JSON"
                oninput="validarJson()"
                onkeydown="handleTab(event)"
                spellcheck="false"></textarea>
        </div>
        <div class="editor-status">
            <span id="json-error"></span>
            <span id="editor-chars">0 chars</span>
        </div>
    </div>

    <!-- ── Panel Gmail: buscar por factura ── -->
    <div class="gmail-panel">
        <div class="gmail-panel-header">
            <svg style="width:13px;height:13px;flex-shrink:0;" viewBox="0 0 24 24" fill="currentColor">
                <path d="M20 4H4a2 2 0 00-2 2v12a2 2 0 002 2h16a2 2 0 002-2V6a2 2 0 00-2-2zm0 4.236l-8 4.882-8-4.882V6h16v2.236z"/>
            </svg>
            Gmail — Buscar Factura
        </div>
        <div class="gmail-search-box">
            <input type="text" id="gmail-factura" placeholder="Nº factura en asunto..."
                   onkeydown="if(event.key==='Enter') buscarGmail()">
            <button id="gmail-btn" onclick="buscarGmail()">Buscar</button>
        </div>
        <div class="gmail-status" id="gmail-status" style="display:none;"></div>
        <div class="gmail-results" id="gmail-results">
            <div class="gmail-empty">Ingresa un número de factura para buscar en Gmail.</div>
        </div>
    </div>

    <!-- ── Panel derecho: respuesta ── -->
    <div class="panel-response">
        <div class="response-header">
            <span style="font-weight:600;">Respuesta</span>
            <div class="spinner" id="spinner"></div>
            <span class="status-pill status-0" id="status-pill">—</span>
            <span id="time-label" style="color:#64748b; font-size:11px; margin-left:auto;"></span>
        </div>
        <div class="response-tabs">
            <div class="response-tab active" onclick="switchTab('body', this)">Body</div>
            <div class="response-tab" onclick="switchTab('pretty', this)">Pretty</div>
        </div>
        <div class="response-body empty" id="response-body">
            Esperando solicitud...
        </div>
    </div>

</div>
</div>

<script>
const PAYLOAD_URL = '{{ route("sap.test.payload", ":id") }}';
const ENVIAR_URL  = '{{ route("sap.test.enviar") }}';
const CSRF        = '{{ csrf_token() }}';

let currentTab = 'body';
let lastResponse = null;

// ── Buscar documentos ──────────────────────────────────────────────
document.getElementById('doc-search').addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.doc-item').forEach(el => {
        el.style.display = el.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});

// ── Seleccionar documento y cargar payload ─────────────────────────
async function seleccionarDoc(id) {
    document.querySelectorAll('.doc-item').forEach(el => {
        el.classList.toggle('active', parseInt(el.dataset.id) === id);
    });

    document.getElementById('btn-send').disabled = true;
    document.getElementById('doc-label').textContent = 'Cargando...';

    try {
        const url = PAYLOAD_URL.replace(':id', id);
        const res = await fetch(url, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
        });
        const data = await res.json();

        const json = JSON.stringify(data.payload, null, 2);
        document.getElementById('json-editor').value = json;
        document.getElementById('doc-label').textContent =
            `${data.documento.numero} · ${data.documento.proveedor ?? ''} · ${data.documento.items_count} items`;
        document.getElementById('btn-send').disabled = false;
        validarJson();
    } catch (e) {
        document.getElementById('doc-label').textContent = 'Error al cargar';
    }
}

// ── Validar JSON en tiempo real ────────────────────────────────────
function validarJson() {
    const val = document.getElementById('json-editor').value;
    const errEl = document.getElementById('json-error');
    const charsEl = document.getElementById('editor-chars');
    charsEl.textContent = val.length.toLocaleString() + ' chars';

    if (!val.trim()) { errEl.textContent = ''; return; }
    try {
        JSON.parse(val);
        errEl.textContent = '✓ JSON válido';
        errEl.style.color = '#4ade80';
    } catch (e) {
        errEl.textContent = '✗ ' + e.message;
        errEl.style.color = '#f87171';
    }
}

// ── Tab para indentar en el textarea ──────────────────────────────
function handleTab(e) {
    if (e.key === 'Tab') {
        e.preventDefault();
        const ta = e.target;
        const start = ta.selectionStart;
        const end   = ta.selectionEnd;
        ta.value = ta.value.substring(0, start) + '  ' + ta.value.substring(end);
        ta.selectionStart = ta.selectionEnd = start + 2;
    }
}

// ── Formatear JSON ─────────────────────────────────────────────────
function formatearJson() {
    const ta = document.getElementById('json-editor');
    try {
        ta.value = JSON.stringify(JSON.parse(ta.value), null, 2);
        validarJson();
    } catch(e) { alert('JSON inválido, no se puede formatear'); }
}

// ── Copiar JSON ────────────────────────────────────────────────────
function copiarJson() {
    const val = document.getElementById('json-editor').value;
    navigator.clipboard.writeText(val).then(() => {
        const btn = document.querySelector('.btn-secondary');
        btn.textContent = '✓ Copiado';
        setTimeout(() => { btn.textContent = '⎘ Copiar'; }, 1500);
    });
}

// ── Limpiar editor ─────────────────────────────────────────────────
function limpiarEditor() {
    document.getElementById('json-editor').value = '';
    document.getElementById('btn-send').disabled = true;
    document.getElementById('doc-label').textContent = 'Selecciona un documento →';
    document.querySelectorAll('.doc-item').forEach(el => el.classList.remove('active'));
    validarJson();
}

// ── Enviar request ─────────────────────────────────────────────────
async function enviarRequest() {
    const payload = document.getElementById('json-editor').value;

    // Validar JSON antes de enviar
    try { JSON.parse(payload); }
    catch(e) { alert('El JSON no es válido: ' + e.message); return; }

    const btnSend = document.getElementById('btn-send');
    const spinner = document.getElementById('spinner');
    btnSend.disabled = true;
    btnSend.textContent = 'Enviando...';
    spinner.style.display = 'block';

    setResponseEmpty();
    const t0 = Date.now();

    try {
        const res = await fetch(ENVIAR_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': CSRF,
            },
            body: JSON.stringify({ payload }),
        });

        const data = await res.json();
        lastResponse = data;
        const elapsed = Date.now() - t0;

        // Status pill
        const pill = document.getElementById('status-pill');
        pill.textContent = data.http_code || '—';
        pill.className = 'status-pill ' + (
            data.http_code >= 200 && data.http_code < 300 ? 'status-2xx' :
            data.http_code >= 400 && data.http_code < 500 ? 'status-4xx' :
            data.http_code >= 500                         ? 'status-5xx' : 'status-0'
        );

        document.getElementById('time-label').textContent =
            (data.time_ms ?? elapsed) + ' ms';

        renderResponse(data);

    } catch (e) {
        document.getElementById('status-pill').textContent = 'ERR';
        document.getElementById('status-pill').className = 'status-pill status-0';
        mostrarTexto('Error de red: ' + e.message);
    } finally {
        btnSend.disabled = false;
        btnSend.textContent = '▶ Enviar';
        spinner.style.display = 'none';
    }
}

// ── Tabs de respuesta ──────────────────────────────────────────────
function switchTab(tab, el) {
    currentTab = tab;
    document.querySelectorAll('.response-tab').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
    if (lastResponse) renderResponse(lastResponse);
}

function renderResponse(data) {
    if (currentTab === 'pretty') {
        const html = syntaxHighlight(JSON.stringify(data.response ?? data, null, 2));
        const el = document.getElementById('response-body');
        el.innerHTML = html;
        el.className = 'response-body';
    } else {
        const raw = typeof data.response === 'string'
            ? data.response
            : JSON.stringify(data.response ?? data, null, 2);
        mostrarTexto(raw);
    }
}

function mostrarTexto(texto) {
    const el = document.getElementById('response-body');
    el.textContent = texto;
    el.className = 'response-body';
}

function setResponseEmpty() {
    const el = document.getElementById('response-body');
    el.textContent = 'Esperando respuesta...';
    el.className = 'response-body empty';
    document.getElementById('status-pill').textContent = '—';
    document.getElementById('status-pill').className = 'status-pill status-0';
    document.getElementById('time-label').textContent = '';
    lastResponse = null;
}

// ── Gmail: buscar por número de factura ────────────────────────────
const GMAIL_BUSCAR_URL = '{{ route("gmail.buscar") }}';

async function buscarGmail() {
    const factura = document.getElementById('gmail-factura').value.trim();
    if (!factura) return;

    const btn     = document.getElementById('gmail-btn');
    const status  = document.getElementById('gmail-status');
    const results = document.getElementById('gmail-results');

    btn.disabled = true;
    btn.textContent = '...';
    status.style.display = 'none';
    results.innerHTML = '<div class="gmail-empty">Buscando...</div>';

    try {
        const url = GMAIL_BUSCAR_URL + '?factura=' + encodeURIComponent(factura);
        const res = await fetch(url, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
        });
        const data = await res.json();

        if (data.error) {
            results.innerHTML = `<div class="gmail-empty" style="color:#ef4444;">${data.error}</div>`;
            return;
        }

        status.style.display = 'block';
        status.textContent = `${data.total} resultado(s) para "${data.factura}"`;

        if (data.total === 0) {
            results.innerHTML = '<div class="gmail-empty">No se encontraron correos con ese número en el asunto.</div>';
            return;
        }

        // Archivos del ZIP y datos de la factura (primer correo)
        let zipHtml = '';
        const zip = data.archivos_zip;
        if (zip && zip.zip_filename) {
            const fileIcons = { xml: '📄', pdf: '📕' };
            const fileList = zip.archivos.map(f => {
                const ext = f.split('.').pop().toLowerCase();
                const icon = fileIcons[ext] || '📎';
                return `<div style="display:flex;align-items:center;gap:6px;padding:4px 0;border-bottom:1px solid #f0f0f0;font-size:11px;">
                    <span>${icon}</span>
                    <span style="font-family:monospace;color:#1e293b;">${escHtml(f)}</span>
                </div>`;
            }).join('');

            // Datos de la factura electrónica
            let facturaHtml = '';
            const f = zip.factura;
            if (f && !f.error) {
                const prods = (f.productos || []).map(p => `
                    <tr style="border-bottom:1px solid #f0f0f0;">
                        <td style="padding:3px 5px;font-family:monospace;color:#1e40af;white-space:nowrap;">${escHtml(p.codigo)}</td>
                        <td style="padding:3px 5px;color:#1e293b;font-size:10px;">${escHtml(p.descripcion)}</td>
                        <td style="padding:3px 5px;text-align:right;white-space:nowrap;">${escHtml(p.cantidad)}</td>
                        <td style="padding:3px 5px;text-align:right;white-space:nowrap;color:#065f46;">${escHtml(Number(p.valor).toLocaleString('es-CO'))}</td>
                    </tr>`).join('');

                facturaHtml = `
                    <div style="margin-top:8px;padding:8px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:6px;">
                        <div style="font-size:10px;font-weight:700;color:#1e40af;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;">
                            🧾 Factura Electrónica
                        </div>
                        <div style="font-size:11px;margin-bottom:4px;">
                            <span style="color:#6b7280;">NIT:</span>
                            <strong style="color:#1e293b;">${escHtml(f.nit_proveedor)}</strong>
                            &nbsp;·&nbsp;
                            <span style="color:#1e293b;">${escHtml(f.nombre_proveedor)}</span>
                        </div>
                        ${prods ? `<div style="font-size:10px;font-weight:700;color:#374151;margin:6px 0 3px;">Productos (${f.productos.length})</div>
                        <div style="overflow-x:auto;">
                        <table style="width:100%;border-collapse:collapse;font-size:11px;">
                            <thead>
                                <tr style="background:#dbeafe;">
                                    <th style="padding:3px 5px;text-align:left;color:#1e40af;font-size:10px;">Código</th>
                                    <th style="padding:3px 5px;text-align:left;color:#1e40af;font-size:10px;">Descripción</th>
                                    <th style="padding:3px 5px;text-align:right;color:#1e40af;font-size:10px;">Cant.</th>
                                    <th style="padding:3px 5px;text-align:right;color:#1e40af;font-size:10px;">Valor</th>
                                </tr>
                            </thead>
                            <tbody>${prods}</tbody>
                        </table></div>` : ''}
                    </div>`;
            } else if (f && f.error) {
                facturaHtml = `<div style="margin-top:6px;font-size:11px;color:#dc2626;">⚠ ${escHtml(f.error)}</div>`;
            }

            zipHtml = `
                <div style="margin:8px 12px;padding:8px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:6px;">
                    <div style="font-size:10px;font-weight:700;color:#166534;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;">
                        📦 ${escHtml(zip.zip_filename)}
                    </div>
                    ${fileList || '<span style="color:#9ca3af;font-size:11px;">ZIP vacío</span>'}
                    ${facturaHtml}
                </div>`;
        } else if (zip && zip.zip_filename === null) {
            zipHtml = `<div style="margin:8px 12px;font-size:11px;color:#9ca3af;">Sin adjunto ZIP en este correo.</div>`;
        }

        results.innerHTML = zipHtml + data.correos.map(c => `
            <div class="gmail-item ${c.unread ? 'unread' : ''}">
                <div class="gmail-item-subject">
                    ${c.unread ? '<span class="gmail-badge-new"></span>' : ''}
                    ${escHtml(c.subject || '(Sin asunto)')}
                </div>
                <div class="gmail-item-from">${escHtml(c.from)}</div>
                <div class="gmail-item-date">${escHtml(c.date)}</div>
                <div class="gmail-item-snippet">${escHtml(c.snippet)}</div>
            </div>
        `).join('');

    } catch (e) {
        results.innerHTML = `<div class="gmail-empty" style="color:#ef4444;">Error: ${e.message}</div>`;
    } finally {
        btn.disabled = false;
        btn.textContent = 'Buscar';
    }
}

function escHtml(str) {
    return String(str ?? '')
        .replace(/&/g,'&amp;').replace(/</g,'&lt;')
        .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// También buscar al seleccionar un documento (usa el número de factura)
const _origSeleccionarDoc = seleccionarDoc;
seleccionarDoc = async function(id) {
    await _origSeleccionarDoc(id);
    const docItem = document.querySelector(`.doc-item[data-id="${id}"]`);
    if (docItem) {
        const num = docItem.dataset.num;
        if (num) {
            document.getElementById('gmail-factura').value = num;
            buscarGmail();
        }
    }
};

// ── Syntax highlight ───────────────────────────────────────────────
function syntaxHighlight(json) {
    json = json.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    return json.replace(
        /("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g,
        match => {
            if (/^"/.test(match)) {
                return /:$/.test(match)
                    ? `<span class="json-key">${match}</span>`
                    : `<span class="json-str">${match}</span>`;
            }
            if (/true|false/.test(match)) return `<span class="json-bool">${match}</span>`;
            if (/null/.test(match)) return `<span class="json-null">${match}</span>`;
            return `<span class="json-num">${match}</span>`;
        }
    );
}
</script>

</x-app-layout>
