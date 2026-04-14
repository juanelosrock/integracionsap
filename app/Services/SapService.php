<?php

namespace App\Services;

use App\Models\Documento;
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
        ];
    }

    private function construirPayload(Documento $documento): array
    {
        $items = [];
        $posicion = 1;

        foreach ($documento->items as $item) {
            $numLinea = str_pad($posicion * 10, 0); // 10, 20, 30...

            $items[] = [
                'AccountAssignmentCategory'  => 'K',
                'DocumentCurrency'           => $this->currency,
                'PurchaseOrderQuantityUnit'  => trim($item->unidadmedida),
                'Material'                   => trim($item->codarticulo),
                'CompanyCode'                => $this->companyCode,
                'Plant'                      => $documento->codigo_tienda,
                'StorageLocation'            => '',
                'OrderQuantity'              => (float) $item->cantidad,
                'NetPriceAmount'             => 0,
                'PurchaseOrderItemCategory'  => '',
                'IsReturnsItem'              => false,
                'RequirementTracking'        => '',
                'RequisitionerName'          => '',
                '_PurOrdAccountAssignment'   => [
                    [
                        'PurchaseOrderItem'       => (string)($posicion * 10),
                        'AccountAssignmentNumber' => (string)$posicion,
                        'CostCenter'              => $this->costCenter,
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
            'PaymentTerms'            => $this->paymentTerms,
            '_PurchaseOrderItem'      => $items,
        ];
    }
}
