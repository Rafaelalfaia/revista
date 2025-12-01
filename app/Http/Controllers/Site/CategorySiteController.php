<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class CategorySiteController extends Controller
{
    protected function baseSubmissionsQuery()
    {
        return Submission::query();
    }

    public function index()
    {
        $catTable = (new Category)->getTable();

        $categoriesQuery = Category::query();

        if (Schema::hasColumn($catTable, 'is_active')) {
            $categoriesQuery->where('is_active', true);
        }

        if (Schema::hasColumn($catTable, 'sort_order')) {
            $categoriesQuery->orderBy('sort_order');
        }

        if (Schema::hasColumn($catTable, 'name')) {
            $categoriesQuery->orderBy('name');
        }

        $categories = $categoriesQuery->get();

        if (method_exists(Submission::class, 'categories')) {
            foreach ($categories as $category) {
                $count = $this->baseSubmissionsQuery()
                    ->whereHas('categories', function ($q) use ($category) {
                        $q->whereKey($category->getKey());
                    })
                    ->count();

                $category->published_submissions_count = $count;
            }
        } else {
            foreach ($categories as $category) {
                $category->published_submissions_count = 0;
            }
        }

        return view('site.categories.index', compact('categories'));
    }

    public function show(Request $request, Category $category)
    {
        $subTable = (new Submission)->getTable();
        $search   = trim((string) $request->input('q', ''));

        $submissions = collect();

        if (method_exists(Submission::class, 'categories')) {
            $rel = $this->baseSubmissionsQuery()
                ->whereHas('categories', function ($q) use ($category) {
                    $q->whereKey($category->getKey());
                })
                ->with(['categories', 'author', 'user']);

            if ($search !== '') {
                $like = '%' . $search . '%';

                $rel->where(function ($q) use ($like, $subTable) {
                    $q->where($subTable . '.title', 'ILIKE', $like);

                    if (Schema::hasColumn($subTable, 'keywords')) {
                        $q->orWhere($subTable . '.keywords', 'ILIKE', $like);
                    }

                    if (Schema::hasColumn($subTable, 'summary')) {
                        $q->orWhere($subTable . '.summary', 'ILIKE', $like);
                    }
                });
            }

            if (Schema::hasColumn($subTable, 'published_at')) {
                $rel->orderByDesc($subTable . '.published_at');
            } else {
                $rel->orderByDesc($subTable . '.id');
            }

            $submissions = $rel->get();
        }

        $featured = $submissions->take(2);
        $others   = $submissions->slice(2);

        return view('site.categories.show', [
            'category'    => $category,
            'submissions' => $submissions,
            'featured'    => $featured,
            'others'      => $others,
            'query'       => $search,
        ]);
    }
}
