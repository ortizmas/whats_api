<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WppProxyController extends Controller
{
    private string $nodeBaseUrl = 'http://localhost:3000';
    private Client $client;

    public function __construct() {
        $this->client = new Client([
            'base_uri' => $this->nodeBaseUrl,
            'timeout'  => 30,
            'headers'  => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ]
        ]);
    }

    /**
     * Listar workers ativos
     */
    public function getWorkers(): JsonResponse
    {
        try {
            $response = $this->client->get('/workers');
            return response()->json(json_decode($response->getBody(), true));
        } catch (RequestException $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Iniciar sessão
     */
    public function startSession(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'session' => 'required|string',
            'hostname' => 'sometimes|string',
        ]);

        try {
            $response = $this->client->post('/start', [
                'json' => $validated,
            ]);

            return response()->json(json_decode($response->getBody(), true));
        } catch (RequestException $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Enviar mensagem
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'session' => 'required|string',
            'number' => 'required|string',
            'message' => 'required|string',
            'hostname' => 'sometimes|string',
            'random' => 'sometimes|boolean'
        ]);
        try {
            $response = $this->client->post('/send', [
                'json' => $validated,
            ]);

            return response()->json(json_decode($response->getBody(), true));
        } catch (RequestException $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Obter QR Code
     */
    public function getQrCode(string $session, Request $request): mixed
    {
        $base64 = $request->query('base64', false);

        try {
            $url = "/qr/{$session}" . ($base64 ? '?base64=true' : '');
            // return response()->json($url);
            $response = $this->client->get($url);

            if ($base64) {
                return response()->json(json_decode($response->getBody(), true));
            }

            return response($response->getBody())->header('Content-Type', 'text/html');
        } catch (RequestException $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Obter status da sessão
     */
    public function getStatus(string $session): JsonResponse
    {
        try {
            $response = $this->client->get("/status/{$session}");
            return response()->json(json_decode($response->getBody(), true));
        } catch (RequestException $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Documentação Swagger
     */
    public function getDocsJson(): JsonResponse
    {
        try {
            $response = $this->client->get('/docs.json');
            return response()->json(json_decode($response->getBody(), true));
        } catch (RequestException $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Tratamento de exceções
     */
    private function handleException(RequestException $e): JsonResponse
    {
        if ($e->hasResponse()) {
            $stausCode = $e->getResponse()->getStatusCode();
            $errorBody = json_decode($e->getResponse()->getBody(), true);

            return response()->json([
                'error' => $errorBody['error'] ?? 'Erro na comunicação com o serviço Node.js',
                'details' =>  $errorBody
            ], $stausCode);
        }

        return response()->json([
            'error' => 'Serviço Node.js indisponível',
            'message' => $e->getMessage()
        ], 503);
    }
}
