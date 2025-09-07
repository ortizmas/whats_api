<?php

    namespace App\Http\Controllers\Api;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\JsonResponse;
    use Illuminate\Http\Request;

    class WppCurlController extends Controller
    {
        private string $nodeBaseUrl = 'http://localhost:3000';

        /**
         * Listar workers ativos
         */
        public function getWorkers(): JsonResponse
        {
            return $this->sendRequest('GET', '/workers');
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

            return $this->sendRequest('POST', '/start', $validated);
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

            return $this->sendRequest('POST', '/send', $validated);
        }

        /**
         * Obter QR Code
         */
        public function getQrCode(string $session, Request $request): mixed
        {
            $base64 = $request->query('base64', false);
            $url = "/qr/{$session}" . ($base64 ? '?base64=true' : '');

            $rawResponse = $this->sendRequest('GET', $url, [], true);

            // pega conteúdo bruto
            $content = $rawResponse->getData(true)['raw'] ?? null;
            $status  = $rawResponse->status();

            if ($base64) {
                // se for base64, sempre retornamos JSON
                $decoded = json_decode($content, true);
                return response()->json($decoded ?? ['error' => 'QRCode não disponível'], $status);
            }

            // se não for base64, verificar se é JSON de erro
            $decoded = json_decode($content, true);
            if (is_array($decoded)) {
                // Node respondeu com erro
                return response()->json($decoded, $status);
            }

            // caso válido: QR em HTML/texto
            return response($content)->header('Content-Type', 'text/html');
        }

        /**
         * Obter status da sessão
         */
        public function getStatus(string $session): JsonResponse
        {
            return $this->sendRequest('GET', "/status/{$session}");
        }

        /**
         * Documentação Swagger
         */
        public function getDocsJson(): JsonResponse
        {
            return $this->sendRequest('GET', '/docs.json');
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
