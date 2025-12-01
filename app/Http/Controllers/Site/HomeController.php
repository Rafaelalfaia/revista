<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Edition;
use App\Models\Submission;
use App\Models\Category;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Route;

class HomeController extends Controller
{
    public function index()
    {
        $editionTable = (new Edition)->getTable();
        $editionQuery = Edition::query();

        if (Schema::hasColumn($editionTable, 'published_at')) {
            $editionQuery->whereNotNull('published_at')
                ->orderByDesc('published_at');
        } elseif (Schema::hasColumn($editionTable, 'release_date')) {
            $editionQuery->orderByDesc('release_date');
        } else {
            $editionQuery->orderByDesc('id');
        }

        $currentEdition = $editionQuery->first();

        $currentEditionCover = null;

        if ($currentEdition) {
            $disk = $currentEdition->cover_photo_disk
                ?? $currentEdition->profile_photo_disk
                ?? 'public';

            $path = $currentEdition->cover_photo_path
                ?? $currentEdition->profile_photo_path
                ?? null;

            if ($path) {
                $currentEditionCover = Storage::disk($disk)->url($path);
            }
        }

        $featuredArticles = collect();
        $subTable = (new Submission)->getTable();

        if ($currentEdition) {
            $q = $currentEdition->submissions();

            $pivotTable = $q->getTable();
            if (Schema::hasTable($pivotTable) && Schema::hasColumn($pivotTable, 'notes')) {
                $q->wherePivot('notes', 'featured');
            }

            if (Schema::hasColumn($subTable, 'published_at')) {
                $q->orderBy($subTable . '.published_at', 'desc');
            } else {
                $q->orderBy($subTable . '.id', 'desc');
            }

            $featuredArticles = $q->take(6)->get();
        }

        $recentProjects = collect();

        if (Schema::hasTable((new Category)->getTable())) {
            $catTable = (new Category)->getTable();
            $catQ = Category::query();

            if (Schema::hasColumn($catTable, 'slug')) {
                $catQ->where('slug', 'projetos-cientificos');
            }

            if (Schema::hasColumn($catTable, 'name')) {
                $catQ->orWhere('name', 'ILIKE', '%projeto%científic%');
            }

            $projectsCategory = $catQ->first();

            if ($projectsCategory) {
                $q = Submission::published()
                    ->whereHas('categories', function ($q) use ($projectsCategory, $catTable) {
                        $q->where($catTable . '.id', $projectsCategory->id);
                    });

                if (Schema::hasColumn($subTable, 'published_at')) {
                    $q->orderByDesc('published_at');
                } else {
                    $q->orderByDesc($subTable . '.id');
                }

                $recentProjects = $q->take(6)->get();
            }
        }

        $previousEditionsQ = Edition::query()
            ->when($currentEdition, fn($q) => $q->where('id', '!=', $currentEdition->id));

        if (Schema::hasColumn($editionTable, 'published_at')) {
            $previousEditionsQ->whereNotNull('published_at')
                ->orderByDesc('published_at');
        } elseif (Schema::hasColumn($editionTable, 'release_date')) {
            $previousEditionsQ->orderByDesc('release_date');
        } else {
            $previousEditionsQ->orderByDesc('id');
        }

        $previousEditions = $previousEditionsQ->take(8)->get();

                // Categorias destacadas para o bloco "Publicações por área"
        $topCategories = collect();

        if (Schema::hasTable((new Category)->getTable())) {
            $catTable = (new Category)->getTable();
            $catQ = Category::query();

            if (Schema::hasColumn($catTable, 'is_active')) {
                $catQ->where('is_active', true);
            }

            if (Schema::hasColumn($catTable, 'sort_order')) {
                $catQ->orderBy('sort_order');
            }

            if (Schema::hasColumn($catTable, 'name')) {
                $catQ->orderBy('name');
            }

            // principais categorias para exibir na home
            $topCategories = $catQ->take(12)->get();
        }

        $categorySections = collect();

        if ($topCategories->isNotEmpty() && Schema::hasTable($subTable)) {
            $catTable = (new Category)->getTable();

            foreach ($topCategories->take(6) as $category) {

                // BASE DAS PUBLICAÇÕES:
                // 1) Se existir edição atual, usamos as submissões dela (inclui destaques)
                // 2) Se não existir, usamos todas "publicadas"
                if ($currentEdition) {
                    $rel = $currentEdition->submissions()
                        ->with(['categories', 'author', 'user']);
                } else {
                    $rel = Submission::query()
                        ->with(['categories', 'author', 'user']);

                    // Se existir escopo published(), usa ele como critério
                    if (method_exists(Submission::class, 'scopePublished')) {
                        $rel->published();
                    } elseif (Schema::hasColumn($subTable, 'published_at')) {
                        $rel->whereNotNull($subTable . '.published_at');
                    }
                }

                // filtra pela categoria da vez
                $rel->whereHas('categories', function ($q) use ($category, $catTable) {
                    $q->where($catTable . '.id', $category->id);
                });

                // ordenação das publicações na área
                if (Schema::hasColumn($subTable, 'published_at')) {
                    $rel->orderByDesc($subTable . '.published_at');
                } else {
                    $rel->orderByDesc($subTable . '.id');
                }

                // limita a quantidade de cards por área (ex: 6)
                $items = $rel->take(6)->get();

                if ($items->isNotEmpty()) {
                    // injeta a coleção na categoria para o Blade usar
                    $category->setRelation('home_submissions', $items);
                    $categorySections->push($category);
                }
            }
        }


        $stats = [
            'editions' => Edition::count(),
            'articles' => Submission::published()->count(),
        ];

        $submissionRoute = Route::has('autor.submissions.create')
            ? route('autor.submissions.create')
            : (Route::has('login') ? route('login') : '#');

        return view('site.home', [
            'currentEdition'      => $currentEdition,
            'currentEditionCover' => $currentEditionCover,
            'featuredArticles'    => $featuredArticles,
            'recentProjects'      => $recentProjects,
            'previousEditions'    => $previousEditions,
            'topCategories'       => $topCategories,
            'categorySections'    => $categorySections,
            'stats'               => $stats,
            'submissionRoute'     => $submissionRoute,
        ]);
    }
}
