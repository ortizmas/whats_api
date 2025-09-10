<?php

    use App\Http\Controllers\MessageController;
    use Illuminate\Support\Facades\Route;

    Route::get('/', function () {
        return view('welcome', [
            'title' => 'Bem-vindo ao sistema',
            'message' => 'Agora tudo é Blade, sem Vue 🚀'
        ]);
    });


    Route::get('workers', [MessageController::class, 'workers'])->name('workers');
    Route::get('create-session/{hostname}', [MessageController::class, 'createSession'])->name('create-session');
    Route::post('start', [MessageController::class, 'start'])->name('start');
    Route::get('create-qr/{session}', [MessageController::class, 'createQr'])->name('create-qr');
    Route::post('generate-qr', [MessageController::class, 'generateQr'])->name('generate-qr');
    Route::post('send', [MessageController::class, 'send'])->name('send');

    require __DIR__.'/settings.php';
    require __DIR__.'/auth.php';

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
