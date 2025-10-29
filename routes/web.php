<?php

use App\Http\Controllers\ApiDocumentationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::view('/docs', 'swagger', [
    'swaggerJsonUrl' => url('/docs/openapi.json'),
])->name('docs.index');

Route::get('/docs/openapi.json', ApiDocumentationController::class)
    ->name('docs.openapi');
