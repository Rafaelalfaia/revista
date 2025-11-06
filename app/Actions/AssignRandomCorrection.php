<?php

namespace App\Actions;

use App\Models\Submission;
use App\Models\User;
use App\Models\Review;

class AssignRandomCorrection
{

    public static function assign(Submission $submission, int $coordenadorId, ?int $categoryId = null): bool
    {
        $query = User::role('Revisor')
            ->where('created_by_id', $coordenadorId);

        if ($categoryId) {
            $query->whereHas('categories', fn($q) => $q->where('categories.id', $categoryId));
        } else {

            $submissionCatIds = $submission->categories()->pluck('categories.id');
            if ($submissionCatIds->count() > 0) {
                $query->whereHas('categories', fn($q) => $q->whereIn('categories.id', $submissionCatIds));
            }
        }

        $reviewer = $query->orderByRaw('random()')->first();
        if (!$reviewer) return false;

        $review = Review::firstOrNew([
            'submission_id' => $submission->id,
            'reviewer_id'   => $reviewer->id,
        ]);

        $review->status                  = 'revisao_solicitada';
        $review->requested_corrections_at = now();
        $review->save();

        return true;
    }
}
