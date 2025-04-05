<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChunkUploadController extends Controller
{
    public function upload(Request $request): JsonResponse
    {
        $uploadId = $request->input('uploadId');
        $fileName = $request->input('fileName');
        $chunkIndex = $request->input('chunkIndex');
        $totalChunks = $request->input('totalChunks');
        $chunk = $request->file('chunk');

        if (!$uploadId || !$fileName || $chunkIndex === null || !$totalChunks || !$chunk) {
            return response()->json(
                [
                    'success' => false,
                    'error' => 'Missing parameters.'
                ],
                400
            );
        }

        $tempDir = storage_path("app/chunks/{$uploadId}/");

        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        $chunkPath = $tempDir . "chunk_{$chunkIndex}";
        $chunk->move($tempDir, "chunk_{$chunkIndex}");

        $uploadedChunks = glob($tempDir . 'chunk_*');

        if (count($uploadedChunks) == (int)$totalChunks) {
            $finalPath = storage_path('app/uploads/' . basename($fileName));

            if (!is_dir(dirname($finalPath))) {
                mkdir(dirname($finalPath), 0777, true);
            }

            $output = fopen($finalPath, 'wb');

            for ($i = 0; $i < $totalChunks; $i++) {
                $chunkFile = $tempDir . "chunk_{$i}";
                if (!file_exists($chunkFile)) {
                    return response()->json(['success' => false, 'error' => "Missing chunk $i"], 500);
                }

                $input = fopen($chunkFile, 'rb');
                stream_copy_to_stream($input, $output);
                fclose($input);
            }

            fclose($output);

            foreach ($uploadedChunks as $file) {
                unlink($file);
            }

            rmdir($tempDir);
        }

        return response()->json(['success' => true]);
    }
}
