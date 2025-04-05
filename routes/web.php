<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChunkUploadController;

Route::post('/upload/chunk', [ChunkUploadController::class, 'uploadChunk'])->name('upload.chunk');
Route::view('/', 'upload');
