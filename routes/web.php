<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChunkUploadController;

Route::get('/', function () {
    return view('upload');
});

Route::post('/upload', [ChunkUploadController::class, 'upload'])->name('upload');
