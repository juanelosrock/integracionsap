<?php

namespace App\Http\Controllers;

use App\Models\GmailToken;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class GmailController extends Controller
{
    private string $clientId;
    private string $clientSecret;
    private string $redirectUri;

    public function __construct()
    {
        $this->clientId     = config('gmail.client_id');
        $this->clientSecret = config('gmail.client_secret');
        $this->redirectUri  = config('gmail.redirect_uri');
    }

    public function index(): View
    {
        $token   = GmailToken::first();
        $emails  = collect();
        $error   = null;

        if ($token) {
            try {
                if ($token->isExpired()) {
                    $token = $this->refreshToken($token);
                }
                $emails = $this->fetchEmails($token->access_token);
            } catch (\Exception $e) {
                Log::error('Gmail fetch error', ['error' => $e->getMessage()]);
                $error = $e->getMessage();
            }
        }

        return view('gmail.index', compact('token', 'emails', 'error'));
    }

    public function redirect(): RedirectResponse
    {
        $query = http_build_query([
            'client_id'     => $this->clientId,
            'redirect_uri'  => $this->redirectUri,
            'response_type' => 'code',
            'scope'         => 'https://www.googleapis.com/auth/gmail.readonly',
            'access_type'   => 'offline',
            'prompt'        => 'consent',
        ]);

        return redirect('https://accounts.google.com/o/oauth2/v2/auth?' . $query);
    }

    public function callback(Request $request): RedirectResponse
    {
        if ($request->has('error')) {
            return redirect()->route('gmail.index')
                ->with('error', 'Autorización rechazada: ' . $request->input('error'));
        }

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'code'          => $request->input('code'),
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri'  => $this->redirectUri,
            'grant_type'    => 'authorization_code',
        ]);

        if ($response->failed()) {
            Log::error('Gmail token exchange failed', ['body' => $response->body()]);
            return redirect()->route('gmail.index')
                ->with('error', 'Error al obtener token de Gmail.');
        }

        $data = $response->json();

        // Obtener el email de la cuenta autorizada
        $profile = Http::withToken($data['access_token'])
            ->get('https://www.googleapis.com/oauth2/v2/userinfo');

        $email = $profile->json('email', 'unknown@gmail.com');

        GmailToken::updateOrCreate(
            ['email' => $email],
            [
                'access_token'     => $data['access_token'],
                'refresh_token'    => $data['refresh_token'] ?? null,
                'expires_in'       => $data['expires_in'] ?? 3600,
                'token_created_at' => now(),
            ]
        );

        return redirect()->route('gmail.index')
            ->with('success', "Cuenta {$email} conectada correctamente.");
    }

    public function disconnect(): RedirectResponse
    {
        GmailToken::truncate();

        return redirect()->route('gmail.index')
            ->with('success', 'Cuenta de Gmail desconectada.');
    }

    private function refreshToken(GmailToken $token): GmailToken
    {
        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'refresh_token' => $token->refresh_token,
            'grant_type'    => 'refresh_token',
        ]);

        if ($response->failed()) {
            throw new \RuntimeException('No se pudo refrescar el token de Gmail.');
        }

        $data = $response->json();

        $token->update([
            'access_token'     => $data['access_token'],
            'expires_in'       => $data['expires_in'] ?? 3600,
            'token_created_at' => now(),
        ]);

        return $token->fresh();
    }

    public function buscar(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'factura' => ['required', 'string', 'max:100'],
        ]);

        $token = GmailToken::first();

        if (! $token) {
            return response()->json(['error' => 'No hay cuenta de Gmail conectada.'], 422);
        }

        try {
            if ($token->isExpired()) {
                $token = $this->refreshToken($token);
            }

            $factura = trim($request->input('factura'));

            $listResponse = Http::withToken($token->access_token)
                ->get('https://gmail.googleapis.com/gmail/v1/users/me/messages', [
                    'maxResults' => 20,
                    'q'          => 'subject:"' . $factura . '" in:inbox -subject:Fwd: -subject:RV: -subject:Re:',
                ]);

            if ($listResponse->failed()) {
                return response()->json(['error' => 'Error al consultar Gmail.'], 500);
            }

            $messages = collect($listResponse->json('messages', []));

            if ($messages->isEmpty()) {
                return response()->json([
                    'factura'  => $factura,
                    'total'    => 0,
                    'correos'  => [],
                ]);
            }

            $correos = $messages->map(function ($msg) use ($token) {
                $detail = Http::withToken($token->access_token)
                    ->get("https://gmail.googleapis.com/gmail/v1/users/me/messages/{$msg['id']}", [
                        'format'          => 'full',
                        'metadataHeaders' => ['From', 'Subject', 'Date'],
                    ]);

                if ($detail->failed()) return null;

                $headers   = collect($detail->json('payload.headers', []));
                $getHeader = fn($name) => $headers->firstWhere('name', $name)['value'] ?? '';

                return [
                    'id'      => $msg['id'],
                    'from'    => $getHeader('From'),
                    'subject' => $getHeader('Subject'),
                    'date'    => $getHeader('Date'),
                    'snippet' => $detail->json('snippet', ''),
                    'unread'  => in_array('UNREAD', $detail->json('labelIds', [])),
                    'payload' => $detail->json('payload'),
                ];
            })->filter()->values();

            // Tomar el primer correo y extraer archivos del ZIP adjunto
            $archivosZip = [];
            if ($correos->isNotEmpty()) {
                $archivosZip = $this->extraerArchivosDeZip($token->access_token, $correos->first());
            }

            return response()->json([
                'factura'      => $factura,
                'total'        => $correos->count(),
                'correos'      => $correos->map(fn($c) => array_diff_key($c, ['payload' => ''])),
                'archivos_zip' => $archivosZip,
            ]);

        } catch (\Exception $e) {
            Log::error('Gmail buscar error', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function extraerArchivosDeZip(string $accessToken, array $correo): array
    {
        $partes = $this->buscarPartes($correo['payload'] ?? []);

        foreach ($partes as $parte) {
            $filename = $parte['filename'] ?? '';
            $mimeType = $parte['mimeType'] ?? '';

            $esZip = str_ends_with(strtolower($filename), '.zip')
                  || in_array($mimeType, ['application/zip', 'application/x-zip-compressed']);

            if (! $esZip) continue;

            $attachmentId = $parte['body']['attachmentId'] ?? null;
            if (! $attachmentId) continue;

            $attRes = Http::withToken($accessToken)
                ->get("https://gmail.googleapis.com/gmail/v1/users/me/messages/{$correo['id']}/attachments/{$attachmentId}");

            if ($attRes->failed()) continue;

            // Gmail usa base64url — convertir a base64 estándar
            $data = strtr($attRes->json('data', ''), '-_', '+/');
            $zipContent = base64_decode($data);

            if (! $zipContent) continue;

            $tmpZip = tempnam(sys_get_temp_dir(), 'gmail_zip_');
            file_put_contents($tmpZip, $zipContent);

            $zip      = new \ZipArchive();
            $names    = [];
            $facturaXml = null;

            if ($zip->open($tmpZip) === true) {
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $name = $zip->getNameIndex($i);
                    $names[] = $name;
                    if ($facturaXml === null && str_ends_with(strtolower($name), '.xml')) {
                        $facturaXml = $zip->getFromIndex($i);
                    }
                }
                $zip->close();
            }

            unlink($tmpZip);

            $factura = $facturaXml ? $this->parsearFacturaXml($facturaXml) : null;

            return [
                'zip_filename' => $filename,
                'archivos'     => $names,
                'factura'      => $factura,
            ];
        }

        return ['zip_filename' => null, 'archivos' => [], 'factura' => null];
    }

    private function parsearFacturaXml(string $xmlRaw): array
    {
        libxml_use_internal_errors(true);

        $outer = simplexml_load_string($xmlRaw);
        if (! $outer) {
            return ['error' => 'XML inválido'];
        }

        // El Invoice real puede estar embebido como CDATA en cac:Attachment/cac:ExternalReference/cbc:Description
        $ns  = 'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2';
        $nsc = 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2';

        $invoiceXml = null;
        $attachment = $outer->children($ns)->Attachment;
        if ($attachment) {
            $extRef = $attachment->children($ns)->ExternalReference;
            if ($extRef) {
                $desc = (string) $extRef->children($nsc)->Description;
                if ($desc && str_contains($desc, '<Invoice')) {
                    $invoiceXml = $desc;
                }
            }
        }

        // Si no hay CDATA embebido, el XML ya es directamente un Invoice
        $doc = $invoiceXml
            ? simplexml_load_string($invoiceXml)
            : $outer;

        if (! $doc) {
            return ['error' => 'No se pudo parsear el Invoice'];
        }

        $nsCac = 'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2';
        $nsCbc = 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2';

        // NIT y nombre del proveedor (AccountingSupplierParty)
        $supplier = $doc->children($nsCac)->AccountingSupplierParty;
        $party    = $supplier?->children($nsCac)->Party;
        $taxScheme = $party?->children($nsCac)->PartyTaxScheme;
        $nitProveedor    = $taxScheme ? (string) $taxScheme->children($nsCbc)->CompanyID : '';
        $nombreProveedor = $taxScheme ? (string) $taxScheme->children($nsCbc)->RegistrationName : '';

        // Líneas de la factura
        $productos = [];
        foreach ($doc->children($nsCac)->InvoiceLine as $line) {
            $lineId   = (string) $line->children($nsCbc)->ID;
            $cantidad = (string) $line->children($nsCbc)->InvoicedQuantity;
            $valor    = (string) $line->children($nsCbc)->LineExtensionAmount;

            $item       = $line->children($nsCac)->Item;
            $descripcion = $item ? trim((string) $item->children($nsCbc)->Description, '|') : '';
            $codigo      = '';
            if ($item) {
                $sellerId = $item->children($nsCac)->SellersItemIdentification;
                $codigo   = $sellerId ? (string) $sellerId->children($nsCbc)->ID : '';
            }

            $productos[] = [
                'linea'       => $lineId,
                'codigo'      => $codigo,
                'descripcion' => trim($descripcion),
                'cantidad'    => $cantidad,
                'valor'       => $valor,
            ];
        }

        return [
            'nit_proveedor'    => $nitProveedor,
            'nombre_proveedor' => $nombreProveedor,
            'productos'        => $productos,
        ];
    }

    private function buscarPartes(array $payload): array
    {
        $result = [];

        if (! empty($payload['filename']) && ! empty($payload['body'])) {
            $result[] = $payload;
        }

        foreach ($payload['parts'] ?? [] as $part) {
            $result = array_merge($result, $this->buscarPartes($part));
        }

        return $result;
    }

    private function fetchEmails(string $accessToken): \Illuminate\Support\Collection
    {
        // Obtener lista de mensajes (últimos 20)
        $listResponse = Http::withToken($accessToken)
            ->get('https://gmail.googleapis.com/gmail/v1/users/me/messages', [
                'maxResults' => 20,
                'q'          => 'in:inbox',
            ]);

        if ($listResponse->failed()) {
            throw new \RuntimeException('Error al obtener mensajes de Gmail.');
        }

        $messages = collect($listResponse->json('messages', []));

        if ($messages->isEmpty()) {
            return collect();
        }

        // Obtener detalles de cada mensaje
        return $messages->map(function ($msg) use ($accessToken) {
            $detail = Http::withToken($accessToken)
                ->get("https://gmail.googleapis.com/gmail/v1/users/me/messages/{$msg['id']}", [
                    'format' => 'metadata',
                    'metadataHeaders' => ['From', 'Subject', 'Date'],
                ]);

            if ($detail->failed()) {
                return null;
            }

            $headers = collect($detail->json('payload.headers', []));
            $getHeader = fn($name) => $headers->firstWhere('name', $name)['value'] ?? '';

            return [
                'id'       => $msg['id'],
                'from'     => $getHeader('From'),
                'subject'  => $getHeader('Subject'),
                'date'     => $getHeader('Date'),
                'snippet'  => $detail->json('snippet', ''),
                'unread'   => in_array('UNREAD', $detail->json('labelIds', [])),
            ];
        })->filter()->values();
    }
}
