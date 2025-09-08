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

    public function start()
    {

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
