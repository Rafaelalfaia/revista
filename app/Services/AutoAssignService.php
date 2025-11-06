<?php

namespace App\Services;

use App\Models\Submission;
use App\Models\User;
use App\Models\Review;
use Illuminate\Support\Facades\DB;

class AutoAssignService
{
    public function assignByPrimaryCategory(Submission $sub, int $limit = 1): int
    {
        $primaryCatId = $sub->categories()
            ->wherePivot('is_primary', true)
            ->value('categories.id');

        if (!$primaryCatId) return 0;

        $candidatos = User::role('Revisor')
            ->where('users.id', '<>', $sub->user_id)
            ->whereDoesntHave('roles', fn($q) => $q->whereIn('name', ['Admin','Coordenador']))
            ->whereHas('expertiseCategories', fn($q) => $q->where('categories.id', $primaryCatId))

            ->whereNotExists(function ($q) use ($sub) {
                $q->select(DB::raw(1))
                  ->from('reviews')
                  ->whereColumn('reviews.reviewer_id', 'users.id')
                  ->where('reviews.submission_id', $sub->id);
            })

            ->addSelect(['open_reviews_count' => function ($q) {
                $q->selectRaw('COUNT(*)')
                  ->from('reviews')
                  ->whereColumn('reviews.reviewer_id', 'users.id')
                  ->whereIn('status', ['atribuida','em_revisao','revisao_solicitada']);
            }])
            ->orderBy('open_reviews_count')
            ->inRandomOrder()
            ->limit($limit)
            ->get();

        if ($candidatos->isEmpty()) return 0;

        $count = 0;

        DB::transaction(function () use ($sub, $candidatos, &$count) {
            foreach ($candidatos as $rev) {
                Review::firstOrCreate(
                    ['submission_id' => $sub->id, 'reviewer_id' => $rev->id],
                    ['status' => 'atribuida', 'assigned_at' => now()]
                );
                $count++;
            }

            if ($count > 0 && $sub->status !== Submission::ST_REVIEW) {
                $sub->status     = Submission::ST_REVIEW;   // em_revisao
                $sub->triaged_at = $sub->triaged_at ?: now();
                $sub->save();
            }
        });

        return $count;
    }
}
