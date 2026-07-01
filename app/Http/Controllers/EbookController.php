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
        $flatPoints = [];

        $selectedChapter = null;
        $selectedPoint = null;

        foreach ($chapters as $chapter) {
            foreach ($chapter['items'] as $point) {
                $flatPoints[] = [
                    'chapter' => $chapter,
                    'point' => $point,
                ];

                if (($point['slug'] ?? '') === $slug) {
                    $selectedChapter = $chapter;
                    $selectedPoint = $point;
                }
            }
        }

        abort_if(!$selectedPoint, 404);

        $currentIndex = collect($flatPoints)->search(fn (array $entry) => ($entry['point']['slug'] ?? '') === $slug);
        $previousPoint = $currentIndex !== false && $currentIndex > 0 ? $flatPoints[$currentIndex - 1] : null;
        $nextPoint = $currentIndex !== false && $currentIndex < count($flatPoints) - 1 ? $flatPoints[$currentIndex + 1] : null;
        $pointNumber = $currentIndex !== false ? $currentIndex + 1 : null;
        $totalPoints = count($flatPoints);

        return view('ebook.point', [
            'content' => $content,
            'chapter' => $selectedChapter,
            'point' => $selectedPoint,
            'chapterPoints' => $selectedChapter['items'] ?? [],
            'previousPoint' => $previousPoint,
            'nextPoint' => $nextPoint,
            'pointNumber' => $pointNumber,
            'totalPoints' => $totalPoints,
        ]);
    }
}
