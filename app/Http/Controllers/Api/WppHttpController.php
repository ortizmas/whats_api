<?php

    namespace App\Http\Controllers\Api;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\JsonResponse;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Http;

    class WppHttpController extends Controller
    {
        private string $nodeBaseUrl = 'http://localhost:3000';

        /**
         * Listar workers ativos
         */
        public function getWorkers(): JsonResponse
        {
            try {
                $response = Http::acceptJson()->get("{$this->nodeBaseUrl}/workers");
                return response()->json($response->json(), $response->status());
            } catch (\Throwable $e) {
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
                $response = Http::acceptJson()->post("{$this->nodeBaseUrl}/start", $validated);
                return response()->json($response->json(), $response->status());
            } catch (\Throwable $e) {
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
                $response = Http::acceptJson()->post("{$this->nodeBaseUrl}/send", $validated);
                return response()->json($response->json(), $response->status());
            } catch (\Throwable $e) {
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
                $url = "{$this->nodeBaseUrl}/qr/{$session}" . ($base64 ? '?base64=true' : '');
                $response = Http::get($url);

                if ($base64) {
                    return response()->json($response->json(), $response->status());
                }

                return response($response->body())->header('Content-Type', 'text/html');
            } catch (\Throwable $e) {
                return $this->handleException($e);
            }
        }

        /**
         * Obter status da sessão
         */
        public function getStatus(string $session): JsonResponse
        {
            try {
                $response = Http::acceptJson()->get("{$this->nodeBaseUrl}/status/{$session}");
                return response()->json($response->json(), $response->status());
            } catch (\Throwable $e) {
                return $this->handleException($e);
            }
        }

        /**
         * Documentação Swagger
         */
        public function getDocsJson(): JsonResponse
        {
            try {
                $response = Http::acceptJson()->get("{$this->nodeBaseUrl}/docs.json");
                return response()->json($response->json(), $response->status());
            } catch (\Throwable $e) {
                return $this->handleException($e);
            }
        }

        /**
         * Tratamento de exceções
         */
        private function handleException(\Throwable $e): JsonResponse
        {
            return response()->json([
                'error' => 'Erro ao comunicar com Node.js',
                'message' => $e->getMessage(),
            ], 503);
        }
    }
