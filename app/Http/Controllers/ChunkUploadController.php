<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ChunkUploadController extends Controller
{
    public function uploadChunk(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file',
            'index' => 'required|integer',
            'total' => 'required|integer',
            'upload_id' => 'required|string'
        ]);

        $chunk = $request->file('file');
        $index = $request->input('index');
        $total = $request->input('total');
        $uploadId = $request->input('upload_id');

        $chunkPath = storage_path("app/uploads/tmp/{$uploadId}");
        if (!File::exists($chunkPath)) {
            File::makeDirectory($chunkPath, 0775, true);
        }

        $chunk->move($chunkPath, "chunk_{$index}");

        if ($index + 1 == $total) {
            $finalPath = storage_path("app/uploads/{$uploadId}.uploaded");
            $out = fopen($finalPath, 'ab');

            for ($i = 0; $i < $total; $i++) {
                $chunkFile = $chunkPath . "/chunk_{$i}";
                $in = fopen($chunkFile, 'rb');
                stream_copy_to_stream($in, $out);
                fclose($in);
                unlink($chunkFile);
            }

            fclose($out);
            File::deleteDirectory($chunkPath);

            return response()->json(['message' => 'File uploaded successfully']);
        }

        return response()->json(['message' => "Chunk {$index} uploaded"]);
    }
}
