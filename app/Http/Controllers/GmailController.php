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
