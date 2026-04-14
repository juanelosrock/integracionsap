<?php

namespace App\Services;

use App\Models\Documento;
use App\Models\Item;
use App\Models\Serie;
use Illuminate\Support\Facades\Log;

class SapService
{
    private string $url;
    private string $token;
    private string $purchasingOrg;
    private string $purchasingGroup;
    private string $currency;
    private string $paymentTerms;
    private string $companyCode;
    private string $costCenter;

    public function __construct()
    {
        $this->url            = config('sap.url');
        $this->token          = config('sap.token');
        $this->purchasingOrg  = config('sap.purchasing_org');
        $this->purchasingGroup = config('sap.purchasing_group');
        $this->currency       = config('sap.currency');
        $this->paymentTerms   = config('sap.payment_terms');
        $this->companyCode    = config('sap.company_code');
        $this->costCenter     = config('sap.cost_center');
    }

    /**
     * Envía el documento como Purchase Order a SAP via la API de integración.
     * Retorna el body de la respuesta como string, o lanza excepción en fallo de cURL.
     */
    public function enviarOrdenCompra(Documento $documento): array
    {
        $documento->loadMissing(['proveedor', 'items']);

        $payload = $this->construirPayload($documento);

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL            => $this->url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Basic ' . $this->token,
            ],
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error    = curl_error($curl);
        curl_close($curl);

        if ($error) {
            Log::error('SAP cURL error', [
                'documento' => $documento->numero,
                'error'     => $error,
            ]);
            throw new \RuntimeException("Error de conexión con SAP: {$error}");
        }

        $decoded = json_decode($response, true) ?? ['raw' => $response];

        Log::info('SAP Purchase Order enviado', [
            'documento' => $documento->numero,
            'http_code' => $httpCode,
            'response'  => $decoded,
        ]);

        return [
            'http_code' => $httpCode,
            'success'   => $httpCode >= 200 && $httpCode < 300,
            'response'  => $decoded,
            'enviado_at' => now(),
        ];
    }

    public function construirPayload(Documento $documento): array
    {
        // Buscar StorageLocation en series usando los 3 primeros caracteres del codigo_tienda
        $prefijo         = strtoupper(substr($documento->codigo_tienda, 0, 3));
        $serie           = Serie::where('serie', $prefijo)->first();
        $storageLocation = $serie ? trim($serie->storageloc_sap) : '';
        $plant           = $serie ? trim($serie->centro_sap) : '';
        $costCenter      = $serie ? trim($serie->ceco_sap) : '';

        // Traer ref_sap y ultimocoste — TRIM en SQL para ignorar espacios en BD remota
        $codigos  = $documento->items->map(fn($i) => trim($i->codarticulo))->filter()->unique()->values();
        $itemsSap = Item::whereIn(\Illuminate\Support\Facades\DB::raw('TRIM(codarticulo)'), $codigos)
                        ->get()
                        ->keyBy(fn($i) => trim($i->codarticulo));

        $items = [];
        $posicion = 1;

        foreach ($documento->items as $item) {
            $cod           = trim($item->codarticulo);
            $itemSap       = $itemsSap->get($cod);
            $material      = ($itemSap && !empty(trim($itemSap->ref_sap)))
                             ? trim($itemSap->ref_sap)
                             : $cod;
            $netPrice      = $itemSap ? (int) round((float) $itemSap->ultimocoste) : 0;

            $items[] = [
                'AccountAssignmentCategory'  => '',
                'DocumentCurrency'           => $this->currency,
                'PurchaseOrderQuantityUnit'  => trim($item->unidadmedida),
                'Material'                   => $material,
                'CompanyCode'                => $this->companyCode,
                'Plant'                      => $plant,
                'StorageLocation'            => $storageLocation,
                'OrderQuantity'              => (float) $item->cantidad,
                'NetPriceAmount'             => $netPrice,
                'PurchaseOrderItemCategory'  => '',
                'IsReturnsItem'                  => false,
                'UnlimitedOverdeliveryIsAllowed' => true,
                'RequirementTracking'            => '',
                'RequisitionerName'              => '',
                '_PurOrdAccountAssignment'   => [
                    [
                        'PurchaseOrderItem'       => (string)($posicion * 10),
                        'AccountAssignmentNumber' => (string)$posicion,
                        'CostCenter'              => $costCenter,
                    ],
                ],
            ];

            $posicion++;
        }

        return [
            'PurchaseOrderType'       => 'NB',
            'PurchasingOrganization'  => $this->purchasingOrg,
            'PurchasingGroup'         => $this->purchasingGroup,
            'Supplier'                => $documento->proveedor->codigo_sap,
            'DocumentCurrency'        => $this->currency,
            'PaymentTerms'            => $documento->proveedor->terminos_pago ?? $this->paymentTerms,
            '_PurchaseOrderItem'      => $items,
        ];
    }
}
