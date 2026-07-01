<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EbookContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class EbookEditorController extends Controller
{
    public function edit()
    {
        $content = EbookContent::query()->firstOrCreate([], EbookContent::defaultData());
        $content->chapters = EbookContent::normalizeChapters($content->chapters ?? []);

        return view('admin.editor', [
            'content' => $content,
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'badge' => ['required', 'string', 'max:80'],
            'hero_title' => ['required', 'string', 'max:255'],
            'hero_description' => ['required', 'string'],
            'intro_note' => ['nullable', 'string'],
            'cover_image' => ['nullable', 'string', 'max:255'],
            'cover_upload' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'theme_primary' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'theme_secondary' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'theme_accent' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'theme_bg_start' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'theme_bg_end' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'theme_text' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'chapters' => ['required', 'array', 'min:1'],
            'chapters.*.title' => ['required', 'string', 'max:255'],
            'chapters.*.items' => ['required', 'array', 'min:1'],
            'chapters.*.items.*.title' => ['required', 'string', 'max:255'],
            'chapters.*.items.*.content' => ['nullable', 'string'],
            'chapters.*.items.*.documents_existing' => ['nullable', 'array', 'max:5'],
            'chapters.*.items.*.document_upload' => ['nullable', 'array', 'max:5'],
            'chapters.*.items.*.document_upload.*' => ['file', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar', 'max:5120'],
        ]);

        $chapters = EbookContent::normalizeChapters($data['chapters']);

        $usedSlugs = [];

        foreach ($chapters as &$chapter) {
            foreach ($chapter['items'] as &$item) {
                $baseSlug = Str::slug($item['title']);
                $baseSlug = $baseSlug !== '' ? $baseSlug : 'poin';

                $slug = $baseSlug;
                $counter = 2;

                while (in_array($slug, $usedSlugs, true)) {
                    $slug = $baseSlug . '-' . $counter;
                    $counter++;
                }

                $item['slug'] = $slug;
                $usedSlugs[] = $slug;
            }
        }
        unset($chapter, $item);

        if (empty($chapters)) {
            return back()->withErrors([
                'chapters' => 'Minimal harus ada 1 bab yang berisi judul dan poin isi.',
            ])->withInput();
        }

        $content = EbookContent::query()->firstOrCreate([], EbookContent::defaultData());
        $existingChapters = EbookContent::normalizeChapters($content->chapters ?? []);

        $coverImagePath = $data['cover_image'] ?? $content->cover_image;
        if ($request->hasFile('cover_upload')) {
            $directory = public_path('coverebook');

            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }

            $coverFile = $request->file('cover_upload');
            $fileName = 'cover-' . now()->format('YmdHis') . '-' . Str::random(6) . '.' . $coverFile->getClientOriginalExtension();
            $coverFile->move($directory, $fileName);

            $coverImagePath = 'coverebook/' . $fileName;
        }

        foreach ($chapters as $chapterIndex => &$chapter) {
            foreach ($chapter['items'] as $pointIndex => &$item) {
                $storedDocuments = collect(data_get($existingChapters, $chapterIndex . '.items.' . $pointIndex . '.documents', []))
                    ->map(function ($document) {
                        if (!is_array($document)) {
                            return null;
                        }

                        $path = trim((string) data_get($document, 'path', ''));
                        if ($path === '') {
                            return null;
                        }

                        return [
                            'path' => $path,
                            'name' => trim((string) data_get($document, 'name', '')) ?: basename($path),
                        ];
                    })
                    ->filter()
                    ->values()
                    ->keyBy('path');

                $submittedExistingDocuments = collect(data_get($data, 'chapters.' . $chapterIndex . '.items.' . $pointIndex . '.documents_existing', []))
                    ->map(function ($document) {
                        if (!is_array($document)) {
                            return null;
                        }

                        $path = trim((string) data_get($document, 'path', ''));
                        if ($path === '') {
                            return null;
                        }

                        return [
                            'path' => $path,
                        ];
                    })
                    ->filter()
                    ->values();

                $existingDocuments = $submittedExistingDocuments
                    ->map(function (array $document) use ($storedDocuments) {
                        if (!$storedDocuments->has($document['path'])) {
                            return null;
                        }

                        return $storedDocuments->get($document['path']);
                    })
                    ->filter()
                    ->values()
                    ->all();

                $newDocuments = [];
                $uploadFiles = $request->file('chapters.' . $chapterIndex . '.items.' . $pointIndex . '.document_upload', []);

                if (!is_array($uploadFiles)) {
                    $uploadFiles = $uploadFiles ? [$uploadFiles] : [];
                }

                if (count($existingDocuments) + count($uploadFiles) > 5) {
                    return back()->withErrors([
                        'chapters' => 'Setiap poin maksimal boleh memiliki 5 dokumen.',
                    ])->withInput();
                }

                foreach ($uploadFiles as $uploadFile) {
                    if (!$uploadFile) {
                        continue;
                    }

                    $directory = public_path('uploads/ebook-documents');

                    if (!File::exists($directory)) {
                        File::makeDirectory($directory, 0755, true);
                    }

                    $fileName = 'doc-' . now()->format('YmdHis') . '-' . Str::random(8) . '.' . $uploadFile->getClientOriginalExtension();
                    $uploadFile->move($directory, $fileName);

                    $newDocuments[] = [
                        'path' => 'uploads/ebook-documents/' . $fileName,
                        'name' => $uploadFile->getClientOriginalName(),
                    ];
                }

                $item['documents'] = array_values(array_merge($existingDocuments, $newDocuments));
            }
        }
        unset($chapter, $item);

        $content->update([
            'badge' => $data['badge'],
            'hero_title' => $data['hero_title'],
            'hero_description' => $data['hero_description'],
            'intro_note' => $data['intro_note'] ?? '',
            'cover_image' => $coverImagePath,
            'theme_primary' => $data['theme_primary'],
            'theme_secondary' => $data['theme_secondary'],
            'theme_accent' => $data['theme_accent'],
            'theme_bg_start' => $data['theme_bg_start'],
            'theme_bg_end' => $data['theme_bg_end'],
            'theme_text' => $data['theme_text'],
            'chapters' => $chapters,
        ]);

        return redirect()->route('admin.editor')->with('status', 'Konten eBook berhasil diperbarui.');
    }
}
