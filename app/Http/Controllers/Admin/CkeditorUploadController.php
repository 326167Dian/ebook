<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CkeditorUploadController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'upload' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
        ]);

        $file = $request->file('upload');
        $directory = public_path('uploads/ebook-editor');

        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $filename = now()->format('YmdHis') . '-' . Str::random(8) . '.' . $file->getClientOriginalExtension();
        $file->move($directory, $filename);

        return response()->json([
            'url' => asset('uploads/ebook-editor/' . $filename),
        ]);
    }
}
