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
    Route::get('start', [MessageController::class, 'start'])->name('start');

    require __DIR__.'/settings.php';
    require __DIR__.'/auth.php';

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
