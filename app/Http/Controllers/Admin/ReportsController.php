<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\{Submission, Review, ReviewAssignment, Edition, Category, User};

class ReportsController extends Controller
{
    public function __construct(){ $this->middleware('can:reports.view'); }

    public function index(Request $r)
    {
        [$from, $to, $filters] = $this->filters($r);
        $kpis    = $this->kpis($from, $to, $filters);
        $funnel  = $this->funnel($from, $to, $filters);
        $monthly = $this->submissionsPerMonth($from, $to, $filters);
        $overdue = $this->overdueReviews($from, $to, $filters, 5);

        return view('admin.reports.index', array_merge(
            compact('kpis','funnel','monthly','overdue','from','to','filters'),
            $this->filterSources()
        ));
    }


    public function authors(Request $r){
        [$from, $to, $filters] = $this->filters($r);
        $leaders = $this->topAuthors($from, $to, $filters);
        $acceptance = $this->authorsAcceptance($from, $to, $filters);
        return view('admin.reports.authors', compact('leaders','acceptance','from','to','filters'));
    }

    public function reviewers(Request $r){
        [$from, $to, $filters] = $this->filters($r);
        $load = $this->reviewersLoad($from, $to, $filters);
        $sla  = $this->reviewersSla($from, $to, $filters);
        return view('admin.reports.reviewers', compact('load','sla','from','to','filters'));
    }

    public function coordinators(Request $r){
        [$from, $to, $filters] = $this->filters($r);
        $ops = $this->coordinatorsOps($from, $to, $filters);
        return view('admin.reports.coordinators', compact('ops','from','to','filters'));
    }

    public function submissions(Request $r){
        [$from, $to, $filters] = $this->filters($r);
        $byStatus = $this->submissionsByStatus($from, $to, $filters);
        $byCategory = $this->submissionsByCategory($from, $to, $filters);
        $leadTimes = $this->leadTimes($from, $to, $filters);
        return view('admin.reports.submissions', compact('byStatus','byCategory','leadTimes','from','to','filters'));
    }

    public function editions(Request $r){
        [$from, $to, $filters] = $this->filters($r);
        $ed = $this->editionsStats($from, $to, $filters);
        $cat = $this->categoriesDistribution($from, $to, $filters);
        return view('admin.reports.editions', compact('ed','cat','from','to','filters'));
    }

    public function system(Request $r){
        [$from, $to, $filters] = $this->filters($r);
        $sys = $this->systemHealth($from, $to, $filters);
        return view('admin.reports.system', compact('sys','from','to','filters'));
    }

    private function filters(Request $r): array
    {
        $from = $r->date('from') ?: now()->startOfMonth();
        $to   = $r->date('to')   ?: now();
        $filters = [
            'category_id'  => $r->integer('category_id') ?: null,
            'edition_id'   => $r->integer('edition_id') ?: null,
            'coordinator'  => $r->integer('coordinator_id') ?: null,
            'reviewer'     => $r->integer('reviewer_id') ?: null,
        ];
        return [$from, $to, $filters];
    }

    private function filterSources(): array
    {
        return [
            'categories' => Category::orderBy('name')->get(['id','name']),
            'editions'   => Edition::orderByDesc('release_date')->get(['id','title','release_date']),
        ];
    }

    private function cacheKey(string $k, $from, $to, array $f): string {
        return 'rpt:'.$k.':'.md5(json_encode([$from?->toDateString(),$to?->toDateString(),$f]));
    }

    private function kpis($from,$to,$f){
        return Cache::remember($this->cacheKey('kpis',$from,$to,$f), 300, function() use($from,$to,$f){
            $base = Submission::query()
                ->when($f['category_id'], fn($q)=>$q->whereHas('categories', fn($c)=>$c->where('categories.id',$f['category_id'])))
                ->when($f['edition_id'],  fn($q)=>$q->whereHas('editions', fn($e)=>$e->where('editions.id',$f['edition_id'])))
                ->whereBetween('created_at', [$from, $to]);
            $total = (clone $base)->count();
            $pub   = (clone $base)->where('status','publicado')->count();
            $acc   = (clone $base)->where('status','aceito')->count();
            $rej   = (clone $base)->where('status','rejeitado')->count();
            $rate  = $total ? round($acc/$total*100,1) : 0;

            $revOpen = Review::whereBetween('created_at',[$from,$to])
                ->when($f['reviewer'], fn($q)=>$q->where('reviewer_id',$f['reviewer']))
                ->whereIn('status',['atribuida','em_revisao'])
                ->count();

            return compact('total','pub','acc','rej','rate','revOpen');
        });
    }

    private function funnel($from,$to,$f){
        return Cache::remember($this->cacheKey('funnel',$from,$to,$f), 300, function() use($from,$to,$f){
            $st = ['rascunho','submetido','em_triagem','em_revisao','revisao_solicitada','aceito','publicado','rejeitado'];
            $rows = Submission::select('status', DB::raw('count(*) as n'))
                ->when($f['category_id'], fn($q)=>$q->whereHas('categories', fn($c)=>$c->where('categories.id',$f['category_id'])))
                ->when($f['edition_id'],  fn($q)=>$q->whereHas('editions', fn($e)=>$e->where('editions.id',$f['edition_id'])))
                ->whereBetween('created_at',[$from,$to])
                ->groupBy('status')->pluck('n','status')->all();
            return collect($st)->map(fn($s)=>['status'=>$s,'n'=>$rows[$s]??0])->values();
        });
    }

    private function submissionsPerMonth($from,$to,$f){
        return Cache::remember($this->cacheKey('subm_month',$from,$to,$f), 300, function() use($from,$to,$f){
            return Submission::selectRaw("strftime('%Y-%m', created_at) AS m, count(*) as n")
                ->when($f['category_id'], fn($q)=>$q->whereHas('categories', fn($c)=>$c->where('categories.id',$f['category_id'])))
                ->whereBetween('created_at',[$from,$to])
                ->groupBy(DB::raw("strftime('%Y-%m', created_at)"))
                ->orderBy(DB::raw("strftime('%Y-%m', created_at)"))
                ->get();
        });
    }

    private function overdueReviews($from,$to,$f,$limit=10){
        return Cache::remember($this->cacheKey('overdue',$from,$to,$f), 300, function() use($from,$to,$f,$limit){
            return Review::with(['submission:id,title,slug','reviewer:id,name'])
                ->whereNotNull('due_at')
                ->where('due_at','<', now())
                ->whereIn('status',['atribuida','em_revisao'])
                ->when($f['reviewer'], fn($q)=>$q->where('reviewer_id',$f['reviewer']))
                ->orderBy('due_at')->limit($limit)->get(['id','submission_id','reviewer_id','status','due_at']);
        });
    }

    private function topAuthors($from,$to,$f){
        return Cache::remember($this->cacheKey('top_authors',$from,$to,$f), 300, function() use($from,$to,$f){
            return User::select('users.id','users.name', DB::raw('count(s.id) as subs'))
                ->join('submissions as s','s.user_id','=','users.id')
                ->when($f['category_id'], fn($q)=>$q->whereExists(function($qq) use($f){
                    $qq->select(DB::raw(1))->from('category_submission as cs')
                       ->whereColumn('cs.submission_id','s.id')->where('cs.category_id',$f['category_id']);
                }))
                ->whereBetween('s.created_at',[$from,$to])
                ->groupBy('users.id','users.name')->orderByDesc('subs')->limit(10)->get();
        });
    }

    private function reviewersLoad($from,$to,$f){
        return Cache::remember($this->cacheKey('rev_load',$from,$to,$f), 300, function() use($from,$to,$f){
            return Review::select('reviewer_id',
                    DB::raw("sum(case when status in ('atribuida','em_revisao') then 1 else 0 end) as abertas"),
                    DB::raw("sum(case when status='parecer_enviado' then 1 else 0 end) as concluídas")
                )
                ->when($f['category_id'], fn($q)=>$q->whereHas('submission.categories', fn($c)=>$c->where('categories.id',$f['category_id'])))
                ->whereBetween('created_at',[$from,$to])
                ->groupBy('reviewer_id')
                ->with('reviewer:id,name')
                ->orderByDesc('abertas')->get();
        });
    }

    private function reviewersSla($from,$to,$f){
        return Cache::remember($this->cacheKey('rev_sla',$from,$to,$f), 300, function() use($from,$to,$f){
            return Review::select('reviewer_id',
                DB::raw("avg(extract(epoch from (submitted_opinion_at - assigned_at))/3600.0) as horas_media")
            )
            ->whereNotNull('assigned_at')->whereNotNull('submitted_opinion_at')
            ->when($f['category_id'], fn($q)=>$q->whereHas('submission.categories', fn($c)=>$c->where('categories.id',$f['category_id'])))
            ->whereBetween('assigned_at',[$from,$to])
            ->groupBy('reviewer_id')
            ->with('reviewer:id,name')->orderBy('horas_media')->get();
        });
    }

    private function submissionsByStatus($from,$to,$f){
        return Submission::select('status', DB::raw('count(*) as n'))
            ->when($f['category_id'], fn($q)=>$q->whereHas('categories', fn($c)=>$c->where('categories.id',$f['category_id'])))
            ->whereBetween('created_at',[$from,$to])
            ->groupBy('status')->orderBy('status')->get();
    }

    private function submissionsByCategory($from,$to,$f){
        return DB::table('category_submission as cs')
            ->join('categories as c','c.id','=','cs.category_id')
            ->join('submissions as s','s.id','=','cs.submission_id')
            ->when($f['category_id'], fn($q)=>$q->where('c.id',$f['category_id']))
            ->whereBetween('s.created_at',[$from,$to])
            ->select('c.id','c.name', DB::raw('count(*) as n'))
            ->groupBy('c.id','c.name')->orderByDesc('n')->get();
    }

    private function leadTimes($from,$to,$f){
        return DB::table('submissions as s')
            ->when($f['category_id'], fn($q)=>$q->whereExists(function($qq) use($f){
                $qq->select(DB::raw(1))->from('category_submission as cs')
                   ->whereColumn('cs.submission_id','s.id')->where('cs.category_id',$f['category_id']);
            }))
            ->whereBetween('s.created_at',[$from,$to])
            ->selectRaw("
                avg(extract(epoch from (coalesce(s.triaged_at, s.created_at) - s.created_at))/3600.0) as h_triagem,
                avg(extract(epoch from (coalesce(s.accepted_at, s.updated_at) - s.created_at))/3600.0) as h_ate_decisao
            ")->first();
    }

    private function editionsStats($from,$to,$f){
        return Edition::select('id','title','slug')
            ->withCount(['submissions as artigos'=>function($q) use($from,$to){
                $q->whereBetween('edition_submission.created_at',[$from,$to]);
            }])->orderByDesc('artigos')->get();
    }

    private function categoriesDistribution($from,$to,$f){
        return $this->submissionsByCategory($from,$to,$f);
    }

    private function systemHealth($from,$to,$f){
        $jobs = DB::table('jobs')->count();
        $failed = DB::table('failed_jobs')->count();
        $cache = DB::table('cache')->count();
        $users = DB::table('users')->count();
        $subs30 = Submission::where('created_at','>=', now()->subDays(30))->count();
        return compact('jobs','failed','cache','users','subs30');
    }

    public function exportCsv(Request $r){ /* gerar CSV da seção atual com filtros */ }
    public function exportPdf(Request $r){ /* renderizar view -> PDF */ }
}
