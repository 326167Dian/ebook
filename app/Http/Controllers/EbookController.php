<?php

namespace App\Http\Controllers;

use App\Models\EbookContent;
use Illuminate\Support\Facades\File;

class EbookController extends Controller
{
    public function index()
    {
        $covers = [];
        $coverDirectory = public_path('coverebook');

        if (File::exists($coverDirectory)) {
            foreach (File::files($coverDirectory) as $file) {
                if (in_array(strtolower($file->getExtension()), ['jpg', 'jpeg', 'png', 'webp'], true)) {
                    $covers[] = 'coverebook/' . $file->getFilename();
                }
            }
        }

        if (empty($covers) && File::exists(public_path('coverebook.png'))) {
            $covers[] = 'coverebook.png';
        }

        $content = EbookContent::query()->first();

        if (!$content) {
            $content = new EbookContent(EbookContent::defaultData());
        }

        $content->chapters = EbookContent::normalizeChapters($content->chapters ?? []);

        return view('ebook.index', [
            'covers' => $covers,
            'content' => $content,
        ]);
    }

    public function point(string $slug)
    {
        $content = EbookContent::query()->first();

        if (!$content) {
            $content = new EbookContent(EbookContent::defaultData());
        }

        $chapters = EbookContent::normalizeChapters($content->chapters ?? []);

        $selectedChapter = null;
        $selectedPoint = null;

        foreach ($chapters as $chapter) {
            foreach ($chapter['items'] as $point) {
                if (($point['slug'] ?? '') === $slug) {
                    $selectedChapter = $chapter;
                    $selectedPoint = $point;
                    break 2;
                }
            }
        }

        abort_if(!$selectedPoint, 404);

        return view('ebook.point', [
            'content' => $content,
            'chapter' => $selectedChapter,
            'point' => $selectedPoint,
        ]);
    }
}
