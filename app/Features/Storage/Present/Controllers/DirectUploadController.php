<?php

namespace App\Features\Storage\Present\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DirectUploadController
{
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240',
            'file_path' => 'required|string',
        ]);
        $file = $request->file('file');
        $filePath = $request->input('file_path');
        Storage::disk('public')->putFileAs(
            dirname($filePath),
            $file,
            basename($filePath)
        );

        return response()->json();
    }
}
