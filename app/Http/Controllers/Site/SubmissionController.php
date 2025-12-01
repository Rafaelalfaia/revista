<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends Controller
{
    public function show(Submission $submission)
    {
        $submission->loadMissing(['categories', 'author']);

        $edition = null;
        if (method_exists($submission, 'editions')) {
            $edition = $submission->editions()->orderByDesc('id')->first();
        }

        $editionCover = null;
        if ($edition) {
            $disk = $edition->cover_photo_disk
                ?? $edition->profile_photo_disk
                ?? 'public';

            $path = $edition->cover_photo_path
                ?? $edition->profile_photo_path
                ?? null;

            if ($path) {
                $editionCover = Storage::disk($disk)->url($path);
            }
        }

        $reviews = method_exists($submission, 'reviews')
            ? $submission->reviews()->with('reviewer')->get()
            : collect();

        $reviewersList = $reviews
            ->map(fn ($review) => optional($review->reviewer)->name)
            ->filter()
            ->unique()
            ->values()
            ->implode(', ');

        $body = $submission->body
            ?? $submission->conteudo_html
            ?? $submission->conteudo
            ?? null;

        return view('site.submissions.show', [
            'submission'     => $submission,
            'edition'        => $edition,
            'editionCover'   => $editionCover,
            'reviewersList'  => $reviewersList,
            'body'           => $body,
        ]);
    }
}
