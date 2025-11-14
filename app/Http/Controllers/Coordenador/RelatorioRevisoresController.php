<?php

namespace App\Http\Controllers\Coordenador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Category;
use App\Models\Review;

class RelatorioRevisoresController extends Controller
{
    public function index(Request $r)
    {
        $uid = $r->user()->id;
        $from = $r->filled('from') ? Carbon::parse($r->input('from').' 00:00:00') : Carbon::now()->subDays(30)->startOfDay();
        $to = $r->filled('to') ? Carbon::parse($r->input('to').' 23:59:59') : Carbon::now()->endOfDay();
        $catId = $r->input('category_id');
        $status = $r->input('status');
        $slaDays = (int) (config('trv.review_sla_days') ?? env('TRV_REVIEW_SLA_DAYS') ?? 14);

        $managedReviewerIds = $this->managedReviewers($uid, $from, $to);

        $base = Review::query()
            ->when(!empty($managedReviewerIds), fn($q) => $q->whereIn('reviewer_id', $managedReviewerIds))
            ->when($status, fn($q) => $q->where('status', $status))
            ->whereBetween('reviews.created_at', [$from, $to])
            ->when($catId, function($q) use ($catId) {
                $q->whereHas('submission.categories', fn($c) => $c->where('categories.id', $catId));
            });

        $resumo = [
            'total'              => (clone $base)->count(),
            'atribuida'          => (clone $base)->where('status', 'atribuida')->count(),
            'em_revisao'         => (clone $base)->where('status', 'em_revisao')->count(),
            'revisao_solicitada' => (clone $base)->where('status', 'revisao_solicitada')->count(),
            'parecer_enviado'    => (clone $base)->where('status', 'parecer_enviado')->count(),
        ];

        $porRevisor = (clone $base)
            ->select([
                'reviews.reviewer_id',
                DB::raw('count(*) as total'),
                DB::raw("sum(case when reviews.status='atribuida' then 1 else 0 end) as atribuida"),
                DB::raw("sum(case when reviews.status='em_revisao' then 1 else 0 end) as em_revisao"),
                DB::raw("sum(case when reviews.status='revisao_solicitada' then 1 else 0 end) as revisao_solicitada"),
                DB::raw("sum(case when reviews.status='parecer_enviado' then 1 else 0 end) as parecer_enviado"),
                DB::raw('max(reviews.updated_at) as ultima_atividade'),
            ])
            ->groupBy('reviews.reviewer_id')
            ->get();

        $ids = $porRevisor->pluck('reviewer_id')->all();

        $extras = Review::query()
            ->when(!empty($managedReviewerIds), fn($q) => $q->whereIn('reviewer_id', $managedReviewerIds))
            ->whereBetween('reviews.created_at', [$from, $to])
            ->when($catId, function($q) use ($catId) {
                $q->whereHas('submission.categories', fn($c) => $c->where('categories.id', $catId));
            })
            ->whereIn('reviewer_id', $ids)
            ->select([
                'reviewer_id',
                DB::raw("avg(extract(epoch from (now() - reviews.created_at))/86400) filter (where reviews.status in ('atribuida','em_revisao','revisao_solicitada')) as idade_media_pendentes"),
                DB::raw("sum(case when reviews.status='parecer_enviado' and reviews.updated_at >= '".Carbon::now()->subDays(30)->toDateTimeString()."' then 1 else 0 end) as throughput_30d"),
                DB::raw("sum(case when reviews.status in ('atribuida','em_revisao','revisao_solicitada') and reviews.created_at <= '".Carbon::now()->subDays($slaDays)->toDateTimeString()."' then 1 else 0 end) as atrasadas_sla"),
            ])
            ->groupBy('reviewer_id')
            ->get()
            ->keyBy('reviewer_id');

        $users = User::whereIn('id', $ids)->get(['id','name','email'])->keyBy('id');

        $colecao = $porRevisor->map(function($row) use ($users, $extras) {
            $u = $users->get($row->reviewer_id);
            $ex = $extras->get($row->reviewer_id);
            $pendentes = (int)$row->atribuida + (int)$row->em_revisao + (int)$row->revisao_solicitada;
            return (object)[
                'reviewer_id'        => $row->reviewer_id,
                'nome'               => $u?->name ?? '—',
                'email'              => $u?->email ?? '',
                'total'              => (int)$row->total,
                'atribuida'          => (int)$row->atribuida,
                'em_revisao'         => (int)$row->em_revisao,
                'revisao_solicitada' => (int)$row->revisao_solicitada,
                'parecer_enviado'    => (int)$row->parecer_enviado,
                'pendentes'          => $pendentes,
                'ultima_atividade'   => $row->ultima_atividade ? Carbon::parse($row->ultima_atividade) : null,
                'idade_media_pend'   => $ex?->idade_media_pendentes ? round((float)$ex->idade_media_pendentes,1) : 0.0,
                'throughput_30d'     => (int)($ex->throughput_30d ?? 0),
                'atrasadas_sla'      => (int)($ex->atrasadas_sla ?? 0),
            ];
        })->sortByDesc('pendentes')->values();

        $categorias = Category::orderBy('name')->get(['id','name']);

        return view('coordenador.relatorios.revisores.index', [
            'resumo'     => $resumo,
            'revisores'  => $colecao,
            'categorias' => $categorias,
            'filtros'    => [
                'from' => $from,
                'to'   => $to,
                'category_id' => $catId,
                'status' => $status,
            ],
            'sla_dias'   => $slaDays,
        ]);
    }

    public function show(Request $r, int $reviewerId)
    {
        $uid = $r->user()->id;
        $from = $r->filled('from') ? Carbon::parse($r->input('from').' 00:00:00') : Carbon::now()->subDays(30)->startOfDay();
        $to = $r->filled('to') ? Carbon::parse($r->input('to').' 23:59:59') : Carbon::now()->endOfDay();
        $catId = $r->input('category_id');

        $managedReviewerIds = $this->managedReviewers($uid, $from, $to);
        if (!empty($managedReviewerIds) && !in_array($reviewerId, $managedReviewerIds, true)) {
            abort(403);
        }

        $base = Review::query()
            ->where('reviewer_id', $reviewerId)
            ->whereBetween('reviews.created_at', [$from, $to])
            ->when($catId, function($q) use ($catId) {
                $q->whereHas('submission.categories', fn($c) => $c->where('categories.id', $catId));
            });

        $stats = [
            'total'              => (clone $base)->count(),
            'atribuida'          => (clone $base)->where('status', 'atribuida')->count(),
            'em_revisao'         => (clone $base)->where('status', 'em_revisao')->count(),
            'revisao_solicitada' => (clone $base)->where('status', 'revisao_solicitada')->count(),
            'parecer_enviado'    => (clone $base)->where('status', 'parecer_enviado')->count(),
        ];

        $pendentes = (clone $base)
            ->whereIn('status', ['atribuida','em_revisao','revisao_solicitada'])
            ->with(['submission:id,title,slug,status'])
            ->orderBy('created_at')
            ->paginate(12, ['*'], 'p1');

        $concluidas = (clone $base)
            ->where('status', 'parecer_enviado')
            ->with(['submission:id,title,slug,status'])
            ->orderByDesc('updated_at')
            ->paginate(12, ['*'], 'p2');

        $revisor = User::find($reviewerId, ['id','name','email']);

        return view('coordenador.relatorios.revisores.show', compact('revisor','stats','pendentes','concluidas','from','to','catId'));
    }

    protected function managedReviewers(int $coordinatorId, Carbon $from, Carbon $to): array
    {
        $ids = [];

        if (Schema::hasTable('roles') && Schema::hasTable('model_has_roles')) {
            $roleId = DB::table('roles')->where('name', 'Revisor')->value('id');
            if ($roleId) {
                $userType = addslashes((new User)->getMorphClass());
                $q = DB::table('model_has_roles')
                    ->where('role_id', $roleId)
                    ->where('model_type', $userType);

                if (Schema::hasColumn('users', 'created_by')) {
                    $ids = User::whereIn('id', $q->pluck('model_id'))
                        ->where('created_by', $coordinatorId)
                        ->pluck('id')
                        ->all();
                } else {
                    $ids = $q->pluck('model_id')->all();
                }
            }
        }

        if (empty($ids)) {
            $ids = DB::table('reviews')
                ->whereBetween('created_at', [$from, $to])
                ->distinct()
                ->pluck('reviewer_id')
                ->filter()
                ->values()
                ->all();
        }

        return array_values(array_unique(array_map('intval', $ids)));
    }
}
