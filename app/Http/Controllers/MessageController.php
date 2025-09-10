<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MessageController extends Controller
{
    private string $nodeBaseUrl = 'http://localhost:3000';
    public function index()
    {
        dd('Index');
    }

    public function workers(Request $request)
    {
        $response = $this->sendRequest('GET', '/workers');
        $data = $response->getData(true);
        return view('messages.index', compact('data'));
    }

    public function createSession(Request $request, string $hostname)
    {
        if ($hostname) {
            return view('messages.create-session', compact('hostname'));
        }

        return view('messages.index')->with('message', 'Nome do host invalido');
    }

    public function start(Request $request)
    {
        $validated = $request->validate([
            'session' => 'required|string',
            'hostname' => 'required|sometimes|string',
        ], [
            'session.required' => 'Nome do session invalido',
            'session.string' => 'Nome do session invalido',
            'hostname.required' => 'Nome do host invalido',
            'hostname.string' => 'Nome do host invalido',
        ]);

        $response = $this->sendRequest('POST', '/start', $validated);
        $data = $response->getData(true);
        $data['hostname'] = $validated['hostname'];
        $data['session'] = $validated['session'];

        return view('messages.show', compact('data'));
    }

    public function createQr(Request $request, string $session)
    {
        if ($session) {
            return view('messages.create-qr', compact('session'));
        }

        return view('messages.create-qr')->with('message', 'Nome de sessão invalido');
    }

    public function generateQr(Request $request)
    {
        $request->merge([
            'base64' => filter_var($request->base64, FILTER_VALIDATE_BOOLEAN)
        ]);

        $validated = $request->validate([
            'session' => 'required|string',
            'base64' => 'required|boolean',
        ], [
            'session.required' => 'Nome da sessão é obrigatório',
            'session.string' => 'Nome do session invalido',
            'base64.required' => 'Base64 é obrigatório',
            'base64.boolean' => 'Base64 deve ser um boolean',
        ]);

        $base64 = $request->query('base64', $validated['base64']);
        $url = "/qr/{$validated['session']}" . ($base64 ? '?base64=true' : '');

        $rawResponse = $this->sendRequest('GET', $url, [], true);

        // pega conteúdo bruto
        $content = $rawResponse->getData(true)['raw'] ?? null;
        $status  = $rawResponse->status();

        if ($base64) {
            // se for base64, sempre retornamos JSON
            $decoded = json_decode($content, true);
            return response()->json($decoded ?? ['error' => 'QRCode não disponível 1'], $status);
        }

        // se não for base64, verificar se é JSON de erro
        $decoded = json_decode($content, true);
        if (is_array($decoded)) {
            // Node respondeu com erro
            return response()->json($decoded, $status);
        }

        // caso válido: QR em HTML/texto
        $response = response($content)->header('Content-Type', 'text/html');
        $data = $response->getContent();

        return view('messages.show-qr', compact('data'));
    }

    public function send(Request $request)
    {
        $request->merge([
            'random' => filter_var($request->random, FILTER_VALIDATE_BOOLEAN)
        ]);

        $validated = $request->validate([
            'session' => 'required|string',
            'number' => 'required|string',
            'message' => 'required|string',
            'hostname' => 'sometimes|string',
            'random' => 'sometimes|boolean'
        ], [
            'session.required' => 'Nome do session invalido',
            'session.string' => 'Nome do session invalido',
            'number.required' => 'Numero de telefone invalido',
            'hostname.required' => 'Nome do host invalido',
            'hostname.string' => 'Nome do host invalido',
            'random.boolean' => 'O campo "random" deve ser um boolean',
            'message.required' => 'Nome do message invalido',
        ]);

        $response = $this->sendRequest('POST', '/send', $validated);
        $data = $response->getData(true);
        $data['send'] = true;

        return view('messages.show', compact('data'));
    }

    /**
     * Função central de requisições cURL
     */
    private function sendRequest(string $method, string $uri, array $data = [], bool $raw = false): JsonResponse
    {
        $url = $this->nodeBaseUrl . $uri;

        $ch = curl_init();
        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
        ];

        if (strtoupper($method) === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        } elseif (strtoupper($method) === 'GET' && !empty($data)) {
            $url .= '?' . http_build_query($data);
        }

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => $headers,
        ]);

        $result = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        if ($error) {
            return response()->json([
                'error' => 'Erro ao comunicar com Node.js',
                'message' => $error,
            ], 503);
        }

        if ($raw) {
            return response()->json([
                'raw' => $result
            ], $statusCode ?: 200);
        }

        $decoded = json_decode($result, true);

        return response()->json(
            $decoded ?? ['raw' => $result],
            $statusCode ?: 200
        );
    }
}
