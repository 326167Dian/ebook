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
