<?php

    use App\Http\Controllers\Api\WppCurlController;
    use App\Http\Controllers\Api\WppProxyController;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\Api\PostController;

    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');



    //    GET /api/posts
    //    POST /api/posts
    //    GET /api/posts/{id}clear
    //    PUT /api/posts/{id}
    //    DELETE /api/posts/{id}
    Route::apiResource('posts', PostController::class);

    Route::prefix('wpp')->group(function () {
        // Workers
        Route::get('/workers', [WppProxyController::class, 'getWorkers']);
        // Sessão
        Route::post('/start', [WppProxyController::class, 'startSession']);
        // Mensagem
        Route::post('/send', [WppProxyController::class, 'sendMessage']);
        // QR Code
        Route::get('/qr/{session}', [WppProxyController::class, 'getQrCode']);
        // Status
        // session é o nome do servidor
        Route::get('/status/{session}', [WppProxyController::class, 'getStatus']);
        // Documentação
        Route::get('/docs.json', [WppProxyController::class, 'getDocsJson']);
    });

    Route::prefix('whats')->group(function () {
        // Workers
        Route::get('/workers', [WppCurlController::class, 'getWorkers']);
        // Sessão
        Route::post('/start', [WppCurlController::class, 'startSession']);
        // Mensagem
        Route::post('/send', [WppCurlController::class, 'sendMessage']);
        // QR Code
        Route::get('/qr/{session}', [WppCurlController::class, 'getQrCode']);
        // Status
        // session é o nome do servidor
        Route::get('/status/{session}', [WppCurlController::class, 'getStatus']);
        // Documentação
        Route::get('/docs.json', [WppCurlController::class, 'getDocsJson']);
    });

