<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Edition;
use App\Models\Submission;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class EditionSiteController extends Controller
{
    protected function getCoverUrl(?Edition $edition): ?string
    {
        if (!$edition) {
            return null;
        }

        $disk = $edition->cover_photo_disk
            ?? $edition->profile_photo_disk
            ?? 'public';

        $path = $edition->cover_photo_path
            ?? $edition->profile_photo_path
            ?? null;

        if (!$path) {
            return null;
        }

        return Storage::disk($disk)->url($path);
    }

    public function index()
    {
        $editionTable = (new Edition)->getTable();
        $query        = Edition::query();

        if (Schema::hasColumn($editionTable, 'published_at')) {
            $query->whereNotNull('published_at')
                ->orderByDesc('published_at');
        } elseif (Schema::hasColumn($editionTable, 'release_date')) {
            $query->orderByDesc('release_date');
        } else {
            $query->orderByDesc('id');
        }

        $editions = $query->get();

        foreach ($editions as $edition) {
            $edition->cover_url = $this->getCoverUrl($edition);
        }

        $currentEdition = $editions->first();

        return view('site.editions.index', compact('editions', 'currentEdition'));
    }

    public function show(Edition $edition)
    {
        $coverUrl = $this->getCoverUrl($edition);
        $subTable = (new Submission)->getTable();

        $submissionsQuery = $edition->submissions()
            ->with(['categories', 'author', 'user']);

        if (Schema::hasColumn($subTable, 'published_at')) {
            $submissionsQuery->orderByDesc($subTable . '.published_at');
        } else {
            $submissionsQuery->orderByDesc($subTable . '.id');
        }

        $submissions = $submissionsQuery->get();

        $featured = $submissions->filter(function ($sub) {
            return optional($sub->pivot)->notes === 'featured';
        });

        $others = $submissions->diff($featured);

        return view('site.editions.show', [
            'edition'     => $edition,
            'coverUrl'    => $coverUrl,
            'submissions' => $submissions,
            'featured'    => $featured,
            'others'      => $others,
        ]);
    }
}
