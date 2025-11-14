<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user  = Auth::user();
        $roles = $user?->getRoleNames() ?? collect();

        $from = $request->date('from') ?: now()->subDays(90)->startOfDay();
        $to   = $request->date('to')   ?: now();

        $hasUsers       = Schema::hasTable('users');
        $hasSubmissions = Schema::hasTable('submissions');
        $hasReviews     = Schema::hasTable('reviews');

        $ck = fn(string $k) => 'adm:dash:'.md5(json_encode([$k,$from?->toDateTimeString(),$to?->toDateTimeString()]));

        $kpis = Cache::remember($ck('kpis'), 300, function() use ($hasUsers,$hasSubmissions,$hasReviews) {
            return [
                'users'       => $hasUsers       ? DB::table('users')->count() : 0,
                'submissions' => $hasSubmissions ? DB::table('submissions')->count() : 0,
                'published'   => $hasSubmissions ? DB::table('submissions')->where('status','publicado')->count() : 0,
                'inReview'    => $hasReviews     ? DB::table('reviews')->whereIn('status',['atribuida','em_revisao'])->count() : 0,
                'overdue'     => $hasReviews     ? DB::table('reviews')->whereNotNull('due_at')->where('due_at','<',now())->whereIn('status',['atribuida','em_revisao'])->count() : 0,
            ];
        });

        $byStatus = $hasSubmissions ? Cache::remember($ck('byStatus'), 300, function() use ($from,$to) {
            return DB::table('submissions')
                ->select('status', DB::raw('count(*) as n'))
                ->whereBetween('created_at', [$from,$to])
                ->groupBy('status')
                ->orderBy('status')
                ->get();
        }) : collect();

        $monthly = $hasSubmissions ? Cache::remember($ck('monthly'), 300, function() use ($from,$to) {
            return DB::table('submissions')
                ->selectRaw("date_trunc('month', created_at) as m, count(*) as n")
                ->whereBetween('created_at', [$from,$to])
                ->groupBy(DB::raw("date_trunc('month', created_at)"))
                ->orderBy(DB::raw("date_trunc('month', created_at)"))
                ->get();
        }) : collect();

        $recentUsers = $hasUsers ? Cache::remember($ck('recentUsers'), 300, function() {
            return DB::table('users')->select('id','name','email','created_at')->orderByDesc('created_at')->limit(6)->get();
        }) : collect();

        $recentSubmissions = $hasSubmissions ? Cache::remember($ck('recentSubmissions'), 300, function() {
            return DB::table('submissions')->select('id','title','status','created_at')->orderByDesc('created_at')->limit(6)->get();
        }) : collect();

        $overdueReviews = $hasReviews ? Cache::remember($ck('overdue'), 300, function() {
            return DB::table('reviews as r')
                ->join('submissions as s','s.id','=','r.submission_id')
                ->leftJoin('users as u','u.id','=','r.reviewer_id')
                ->whereNotNull('r.due_at')
                ->where('r.due_at','<', now())
                ->whereIn('r.status',['atribuida','em_revisao'])
                ->orderBy('r.due_at')
                ->limit(6)
                ->get(['r.id','s.title as submission','u.name as reviewer','r.status','r.due_at']);
        }) : collect();

        $topAuthors = ($hasUsers && $hasSubmissions) ? Cache::remember($ck('topAuthors'), 300, function() use ($from,$to) {
            return DB::table('users as u')
                ->join('submissions as s','s.user_id','=','u.id')
                ->whereBetween('s.created_at', [$from,$to])
                ->groupBy('u.id','u.name')
                ->select('u.id','u.name', DB::raw('count(s.id) as subs'))
                ->orderByDesc('subs')->limit(5)->get();
        }) : collect();

        $reviewersLoad = $hasReviews ? Cache::remember($ck('reviewersLoad'), 300, function() use ($from,$to) {
            return DB::table('reviews as r')
                ->leftJoin('users as u','u.id','=','r.reviewer_id')
                ->whereBetween('r.created_at', [$from,$to])
                ->groupBy('r.reviewer_id','u.name')
                ->select('r.reviewer_id','u.name',
                    DB::raw("sum(case when r.status in ('atribuida','em_revisao') then 1 else 0 end) as abertas"),
                    DB::raw("sum(case when r.status='parecer_enviado' then 1 else 0 end) as concluidas")
                )
                ->orderByDesc('abertas')->limit(5)->get();
        }) : collect();

        return view('admin.dashboard', compact(
            'user','roles','from','to','kpis','byStatus','monthly','recentUsers','recentSubmissions','overdueReviews','topAuthors','reviewersLoad',
            'hasUsers','hasSubmissions','hasReviews'
        ));
    }
}
