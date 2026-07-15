<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EbookContent;
use App\Models\PharmacyLogo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FooterEditorController extends Controller
{
    public function edit()
    {
        $content = EbookContent::query()->firstOrCreate([], EbookContent::defaultData());
        $pharmacyLogos = PharmacyLogo::query()->orderBy('sort_order')->orderBy('id')->get();

        return view('admin.footer', [
            'content' => $content,
            'pharmacyLogos' => $pharmacyLogos,
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'author_name' => ['nullable', 'string', 'max:120'],
            'intro_note' => ['nullable', 'string'],
            'author_photo' => ['nullable', 'string', 'max:255'],
            'author_photo_upload' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $content = EbookContent::query()->firstOrCreate([], EbookContent::defaultData());
        $authorPhotoPath = $data['author_photo'] ?? $content->author_photo;

        if ($request->hasFile('author_photo_upload')) {
            $authorPhotoFile = $request->file('author_photo_upload');
            $fileName = 'author-' . now()->format('YmdHis') . '-' . Str::random(6) . '.' . $authorPhotoFile->getClientOriginalExtension();
            Storage::disk('public')->putFileAs('authors', $authorPhotoFile, $fileName);

            $authorPhotoPath = 'storage-public/authors/' . $fileName;
        }

        $content->update([
            'author_name' => trim((string) ($data['author_name'] ?? '')),
            'author_photo' => $authorPhotoPath,
            'intro_note' => $data['intro_note'] ?? '',
        ]);

        return redirect()->route('admin.footer.edit')->with('status', 'Pengaturan footer berhasil diperbarui.');
    }
}
