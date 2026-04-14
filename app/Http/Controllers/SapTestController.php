<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use App\Services\SapService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SapTestController extends Controller
{
    public function index(): View
    {
        $documentos = Documento::with('proveedor')
            ->latest()
            ->limit(100)
            ->get()
            ->map(fn($d) => [
                'id'           => $d->id,
                'numero'       => $d->numero,
                'estado'       => $d->estado,
                'proveedor'    => $d->proveedor?->nombre,
                'codigo_tienda'=> $d->codigo_tienda,
                'fecha'        => $d->fecha?->format('d/m/Y'),
            ]);

        return view('sap.test', compact('documentos'));
    }

    /**
     * Genera el JSON payload para un documento (sin enviarlo).
     */
    public function payload(Documento $documento): JsonResponse
    {
        $documento->load(['proveedor', 'items']);

        $sap = app(SapService::class);

        // Usamos reflexión para acceder al método privado construirPayload
        $ref    = new \ReflectionMethod($sap, 'construirPayload');
        $ref->setAccessible(true);
        $payload = $ref->invoke($sap, $documento);

        return response()->json([
            'documento' => [
                'numero'       => $documento->numero,
                'estado'       => $documento->estado,
                'proveedor'    => $documento->proveedor?->nombre,
                'codigo_tienda'=> $documento->codigo_tienda,
                'fecha'        => $documento->fecha?->format('d/m/Y'),
                'items_count'  => $documento->items->count(),
            ],
            'payload' => $payload,
        ]);
    }

    /**
     * Envía un JSON personalizado a la API SAP.
     */
    public function enviar(Request $request): JsonResponse
    {
        $request->validate([
            'payload' => ['required', 'string'],
        ]);

        // Validar que el payload sea JSON válido
        $decoded = json_decode($request->payload, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json([
                'success'  => false,
                'error'    => 'JSON inválido: ' . json_last_error_msg(),
            ], 422);
        }

        $url   = config('sap.url');
        $token = config('sap.token');

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => $request->payload,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Basic ' . $token,
            ],
        ]);

        $response  = curl_exec($curl);
        $httpCode  = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curlError = curl_error($curl);
        $totalTime = curl_getinfo($curl, CURLINFO_TOTAL_TIME);
        curl_close($curl);

        if ($curlError) {
            return response()->json([
                'success'   => false,
                'http_code' => 0,
                'error'     => $curlError,
                'response'  => null,
                'time_ms'   => round($totalTime * 1000),
            ]);
        }

        $responseDecoded = json_decode($response, true);

        return response()->json([
            'success'   => $httpCode >= 200 && $httpCode < 300,
            'http_code' => $httpCode,
            'response'  => $responseDecoded ?? $response,
            'time_ms'   => round($totalTime * 1000),
        ]);
    }
}
