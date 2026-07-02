<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CkeditorUploadController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'upload' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
        ]);

        $file = $request->file('upload');

        $filename = now()->format('YmdHis') . '-' . Str::random(8) . '.' . $file->getClientOriginalExtension();
        Storage::disk('public')->putFileAs('uploads/ebook-editor', $file, $filename);

        return response()->json([
            'url' => '/storage-public/uploads/ebook-editor/' . $filename,
        ]);
    }
}
