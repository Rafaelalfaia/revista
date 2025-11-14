@extends('console.layout')
@section('title','Dashboard · Admin')
@section('page.title','Painel administrativo')

@php
  $me = auth()->user();
@endphp

@push('head')
<style>
  .shell-admin{max-width:1180px;margin:0 auto;padding:1rem;display:flex;flex-direction:column;gap:1rem}
  .hero-admin{border-radius:1.3rem;border:1px solid var(--line);overflow:hidden;background:
    radial-gradient(circle at top left,rgba(52,211,153,.16),transparent 55%),
    radial-gradient(circle at top right,rgba(59,130,246,.18),transparent 55%),
    var(--panel)}
  .hero-inner{padding:.9rem 1rem 1.1rem;display:flex;align-items:flex-end;justify-content:space-between;gap:.75rem}
  .hero-main{display:flex;flex-direction:column;gap:.25rem;min-width:0}
  .hero-greeting{font-size:.8rem;color:var(--muted)}
  .hero-name{font-size:1rem;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
  .hero-sub{font-size:.75rem;color:var(--muted)}
  .hero-actions{display:flex;flex-wrap:wrap;gap:.4rem;justify-content:flex-end;align-items:center}
  .badge-role{border-radius:999px;padding:.18rem .6rem;font-size:.7rem;border:1px solid rgba(148,163,184,.5);background:var(--panel-2);color:var(--muted);white-space:nowrap}
  .btn-hero{border-radius:.85rem;padding:.4rem .8rem;font-size:.8rem;font-weight:500;display:inline-flex;align-items:center;gap:.3rem;border:1px solid var(--line);background:var(--panel);white-space:nowrap}
  .btn-hero-primary{background:var(--brand);color:#fff;border-color:transparent}
  .hero-metrics{margin-top:.7rem;padding:.55rem .9rem;border-top:1px solid rgba(148,163,184,.4);display:flex;gap:.75rem;font-size:.75rem;flex-wrap:wrap}
  .hero-metric{flex:1;min-width:7rem}
  .hero-metric-label{color:var(--muted)}
  .hero-metric-value{font-weight:600;font-size:.95rem}

  .toolbar-admin{display:flex;flex-wrap:wrap;gap:.5rem;align-items:center;justify-content:flex-end;margin-bottom:.3rem}
  .toolbar-admin .input-pill{width:150px;border-radius:.9rem;border:1px solid var(--line);background:var(--panel);color:var(--text);padding:.5rem .75rem;font-size:.8rem}
  .toolbar-admin .btn-pill{border-radius:.9rem;border:1px solid transparent;background:var(--brand);color:#fff;padding:.5rem 1rem;font-size:.8rem;font-weight:600;white-space:nowrap}

  .kpi-grid{display:grid;gap:.75rem}
  @media(min-width:640px){.kpi-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
  @media(min-width:900px){.kpi-grid{grid-template-columns:repeat(5,minmax(0,1fr))}}
  .kpi-card{border-radius:1rem;border:1px solid var(--line);background:var(--panel);padding:.75rem .9rem;display:grid;gap:.15rem}
  .kpi-label{font-size:.75rem;color:var(--muted)}
  .kpi-value{font-size:1.4rem;font-weight:600}
  .kpi-chip{font-size:.7rem;border-radius:999px;padding:.1rem .6rem;background:var(--soft);border:1px solid var(--line);justify-self:flex-start}

  .section-grid-admin{display:grid;gap:.9rem}
  @media(min-width:1024px){.section-grid-admin{grid-template-columns:2fr 1.4fr}}

  .card-admin{border-radius:1rem;border:1px solid var(--line);background:var(--panel);display:flex;flex-direction:column}
  .card-header-admin{padding:.75rem .9rem;border-bottom:1px solid var(--line);display:flex;align-items:center;justify-content:space-between;gap:.5rem}
  .card-title-admin{font-size:.9rem;font-weight:600}
  .card-sub-admin{font-size:.75rem;color:var(--muted)}
  .card-body-admin{padding:.75rem .9rem}

  .badge-count{border-radius:999px;background:var(--soft);border:1px solid var(--line);padding:.1rem .6rem;font-size:.7rem}

  .bar{height:.7rem;border-radius:999px;border:1px solid var(--line);background:var(--soft);overflow:hidden}
  .bar>i{display:block;height:100%;background:var(--brand)}
  .status-row{display:flex;align-items:center;gap:.6rem}
  .status-chip{background:var(--soft);border:1px solid var(--line);border-radius:.7rem;padding:.18rem .6rem;font-size:.75rem;white-space:nowrap}
  .status-meta{font-size:.75rem;color:var(--muted);margin-top:.15rem}

  .monthly-row{display:grid;grid-template-columns:110px 1fr 64px;align-items:center;gap:.4rem;font-size:.8rem}
  .monthly-label{color:var(--muted)}

  .mini-list{display:flex;flex-direction:column;gap:.55rem;font-size:.8rem}
  .mini-item{border-radius:.9rem;border:1px solid var(--line);background:var(--panel-2);padding:.55rem .65rem;display:flex;align-items:flex-start;justify-content:space-between;gap:.6rem}
  .mini-main{flex:1;min-width:0}
  .mini-title{font-weight:600;line-height:1.35;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
  .mini-meta{margin-top:.15rem;font-size:.72rem;color:var(--muted);display:flex;flex-wrap:wrap;gap:.35rem}
  .mini-side{flex-shrink:0;display:flex;flex-direction:column;align-items:flex-end;gap:.25rem}

  .status-pill{border-radius:999px;padding:.15rem .6rem;font-size:.7rem;font-weight:500;border:1px solid transparent;white-space:nowrap;background:var(--soft)}
  .btn-small{border-radius:.7rem;padding:.23rem .6rem;font-size:.7rem;font-weight:500;border:1px solid var(--line);background:var(--panel);display:inline-flex;align-items:center;gap:.2rem}

  .avatar-user{width:36px;height:36px;border-radius:999px;background:var(--surface);border:1px solid var(--line);display:grid;place-items:center;font-size:.8rem;font-weight:700}
  .user-name{font-size:.85rem;font-weight:600}
  .user-email{font-size:.72rem;color:var(--muted)}
  .user-meta{font-size:.7rem;color:var(--muted)}

  .pillset{display:flex;flex-wrap:wrap;gap:.3rem}
  .pill{border-radius:.75rem;padding:.15rem .55rem;font-size:.72rem;border:1px solid var(--line);background:var(--surface);white-space:nowrap}
  .pill-soft{background:var(--soft)}

  .empty{font-size:.8rem;color:var(--muted);text-align:center;padding:.4rem .3rem}

  @media(max-width:767.98px){
    .hero-inner{flex-direction:column;align-items:flex-start}
    .hero-actions{justify-content:flex-start}
  }
</style>
@endpush

@section('page.actions')
  <form method="get" class="toolbar-admin">
    <input class="input-pill" type="date" name="from" value="{{ request('from', $from->format('Y-m-d')) }}">
    <input class="input-pill" type="date" name="to"   value="{{ request('to',   $to->format('Y-m-d')) }}">
    <button class="btn-pill" type="submit">Aplicar período</button>
  </form>
@endsection

@section('content')
<div class="shell-admin">
  <div class="hero-admin">
    <div class="hero-inner">
      <div class="hero-main">
        <div class="hero-greeting">Olá, {{ $me?->name ?? 'Administrador(a)' }}</div>
        <div class="hero-name">Painel administrativo da Trivento</div>
        <div class="hero-sub">Visão geral de usuários, submissões e fluxo de revisão da revista.</div>
      </div>
      <div class="hero-actions">
        @if (Route::has('admin.users.index'))
          <a href="{{ route('admin.users.index') }}" class="btn-hero">Gerenciar usuários</a>
        @endif
        @if(route::has('admin.submissions.index'))
          <a href="{{ route('admin.submissions.index') }}" class="btn-hero btn-hero-primary">Ver submissões</a>
        @endif
        <span class="badge-role">Admin</span>
      </div>
    </div>
    <div class="hero-metrics">
      <div class="hero-metric">
        <div class="hero-metric-label">Usuários</div>
        <div class="hero-metric-value">{{ $kpis['users'] }}</div>
      </div>
      <div class="hero-metric">
        <div class="hero-metric-label">Submissões</div>
        <div class="hero-metric-value">{{ $kpis['submissions'] }}</div>
      </div>
      <div class="hero-metric">
        <div class="hero-metric-label">Publicadas</div>
        <div class="hero-metric-value">{{ $kpis['published'] }}</div>
      </div>
      <div class="hero-metric">
        <div class="hero-metric-label">Em revisão</div>
        <div class="hero-metric-value">{{ $kpis['inReview'] }}</div>
      </div>
      <div class="hero-metric">
        <div class="hero-metric-label">Revisões em atraso</div>
        <div class="hero-metric-value">{{ $kpis['overdue'] }}</div>
      </div>
    </div>
  </div>

  <div class="kpi-grid">
    <div class="kpi-card">
      <div class="kpi-label">Total de usuários</div>
      <div class="kpi-value">{{ $kpis['users'] }}</div>
      <div class="kpi-chip">Perfis cadastrados no sistema</div>
    </div>
    <div class="kpi-card">
      <div class="kpi-label">Submissões totais</div>
      <div class="kpi-value">{{ $kpis['submissions'] }}</div>
      <div class="kpi-chip">Inclui todas as fases</div>
    </div>
    <div class="kpi-card">
      <div class="kpi-label">Artigos publicados</div>
      <div class="kpi-value">{{ $kpis['published'] }}</div>
      <div class="kpi-chip">Já disponíveis na revista</div>
    </div>
    <div class="kpi-card">
      <div class="kpi-label">Em revisão</div>
      <div class="kpi-value">{{ $kpis['inReview'] }}</div>
      <div class="kpi-chip">Sob análise dos revisores</div>
    </div>
    <div class="kpi-card">
      <div class="kpi-label">Revisões atrasadas</div>
      <div class="kpi-value">{{ $kpis['overdue'] }}</div>
      <div class="kpi-chip">Fora do prazo definido</div>
    </div>
  </div>

  <div class="section-grid-admin">
    <div class="card-admin">
      <div class="card-header-admin">
        <div>
          <div class="card-title-admin">Saúde das submissões</div>
          <div class="card-sub-admin">Distribuição por status e volume mensal no período selecionado.</div>
        </div>
        @php
          $statusTotal = (int) ($byStatus?->sum('n') ?? 0);
          $statusMap = [
            'rascunho'=>'Rascunho','submetido'=>'Submetido','em_triagem'=>'Em triagem','em_revisao'=>'Em revisão',
            'revisao_solicitada'=>'Correções solicitadas','aceito'=>'Aceito','publicado'=>'Publicado','rejeitado'=>'Rejeitado'
          ];
        @endphp
        <span class="badge-count">{{ $statusTotal }} registro{{ $statusTotal === 1 ? '' : 's' }}</span>
      </div>
      <div class="card-body-admin space-y-5">
        <div>
          <div class="card-sub-admin mb-2">Distribuição por status</div>
          @if(($byStatus ?? collect())->isEmpty())
            <div class="empty">Sem dados de status no período.</div>
          @else
            <div class="space-y-2">
              @foreach($byStatus as $row)
                @php
                  $n = (int)($row->n ?? 0);
                  $p = $statusTotal ? (int)round($n*100/$statusTotal) : 0;
                  $lbl = $statusMap[$row->status] ?? ucfirst(str_replace('_',' ', $row->status));
                @endphp
                <div class="status-row">
                  <div class="status-chip min-w-[11rem]">{{ $lbl }}</div>
                  <div class="bar flex-1"><i style="width: {{ $p }}%"></i></div>
                  <div class="text-right tabular-nums text-xs">{{ $n }}</div>
                  <div class="text-right tabular-nums text-xs text-[var(--muted)]">{{ $p }}%</div>
                </div>
              @endforeach
            </div>
          @endif
        </div>

        <div>
          <div class="card-sub-admin mb-2">Submissões por mês</div>
          @php
            $maxN = (int) (($monthly->max(fn($r)=>(int)($r->n ?? 0))) ?? 0);
          @endphp
          @if(($monthly ?? collect())->isEmpty())
            <div class="empty">Sem dados mensais neste intervalo.</div>
          @else
            <div class="space-y-2">
              @foreach($monthly as $r)
                @php
                  $n = (int)($r->n ?? 0);
                  $pct = $maxN > 0 ? (int)round($n*100/$maxN) : 0;
                  $label = \Illuminate\Support\Carbon::parse($r->m)->translatedFormat('M/Y');
                @endphp
                <div class="monthly-row">
                  <div class="monthly-label">{{ $label }}</div>
                  <div class="bar"><i style="width: {{ $pct }}%"></i></div>
                  <div class="text-right tabular-nums text-sm">{{ $n }}</div>
                </div>
              @endforeach
            </div>
          @endif
        </div>
      </div>
    </div>

    <div class="card-admin">
      <div class="card-header-admin">
        <div>
          <div class="card-title-admin">Atividade recente</div>
          <div class="card-sub-admin">Últimas submissões e novos usuários do sistema.</div>
        </div>
      </div>
      <div class="card-body-admin space-y-5">
        <div>
          <div class="card-sub-admin mb-2">Últimas submissões</div>
          @if(($recentSubmissions ?? collect())->isEmpty())
            <div class="empty">Sem submissões recentes.</div>
          @else
            <div class="mini-list">
              @foreach($recentSubmissions as $s)
                @php
                  $created = \Illuminate\Support\Carbon::parse($s->created_at);
                @endphp
                <div class="mini-item">
                  <div class="mini-main">
                    <div class="mini-title">{{ $s->title ?? ('#'.$s->id) }}</div>
                    <div class="mini-meta">
                      <span>Criado em {{ $created->format('d/m/Y H:i') }}</span>
                      <span>Status: {{ ucfirst(str_replace('_',' ', $s->status)) }}</span>
                    </div>
                  </div>
                  <div class="mini-side">
                    <span class="status-pill">{{ ucfirst(str_replace('_',' ', $s->status)) }}</span>
                    @if(route::has('admin.submissions.show'))
                      <a href="{{ route('admin.submissions.show',$s->id) }}" class="btn-small">Ver submissão</a>
                    @endif
                  </div>
                </div>
              @endforeach
            </div>
          @endif
        </div>

        <div>
          <div class="card-sub-admin mb-2">Últimos usuários</div>
          @if(($recentUsers ?? collect())->isEmpty())
            <div class="empty">Sem usuários recentes.</div>
          @else
            <div class="mini-list">
              @foreach($recentUsers as $u)
                @php
                  $parts = preg_split('/\s+/', trim($u->name ?? ''));
                  $ini = mb_strtoupper(mb_substr($parts[0]??'',0,1).mb_substr($parts[1]??'',0,1));
                  $created = \Illuminate\Support\Carbon::parse($u->created_at);
                @endphp
                <div class="mini-item">
                  <div class="mini-main" style="display:flex;gap:.55rem;align-items:center">
                    <div class="avatar-user">{{ $ini ?: 'U' }}</div>
                    <div>
                      <div class="user-name">{{ $u->name ?? '—' }}</div>
                      <div class="user-email">{{ $u->email ?? '—' }}</div>
                      <div class="user-meta">Criado em {{ $created->format('d/m/Y H:i') }}</div>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>

  <div class="section-grid-admin">
    <div class="card-admin">
      <div class="card-header-admin">
        <div>
          <div class="card-title-admin">Revisões em atraso</div>
          <div class="card-sub-admin">Revisões fora do prazo ou em risco.</div>
        </div>
      </div>
      <div class="card-body-admin">
        @if(($overdueReviews ?? collect())->isEmpty())
          <div class="empty">Nenhuma revisão em atraso no período.</div>
        @else
          <div class="mini-list">
            @foreach($overdueReviews as $r)
              @php
                $due = $r->due_at ? \Illuminate\Support\Carbon::parse($r->due_at) : null;
              @endphp
              <div class="mini-item">
                <div class="mini-main">
                  <div class="mini-title">{{ $r->submission ?? '—' }}</div>
                  <div class="mini-meta">
                    @if($r->reviewer)
                      <span>Revisor: {{ $r->reviewer }}</span>
                    @endif
                    @if($due)
                      <span>Vencimento: {{ $due->format('d/m/Y H:i') }}</span>
                    @endif
                  </div>
                </div>
                <div class="mini-side">
                  <span class="status-pill">{{ ucfirst(str_replace('_',' ', $r->status)) }}</span>
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    </div>

    <div class="card-admin">
      <div class="card-header-admin">
        <div>
          <div class="card-title-admin">Autores e carga dos revisores</div>
          <div class="card-sub-admin">Quem mais publica e como está a fila de revisão.</div>
        </div>
      </div>
      <div class="card-body-admin space-y-5">
        <div>
          <div class="card-sub-admin mb-2">Autores em destaque</div>
          @if(($topAuthors ?? collect())->isEmpty())
            <div class="empty">Sem dados de autores no período.</div>
          @else
            <div class="mini-list">
              @foreach($topAuthors as $a)
                <div class="mini-item">
                  <div class="mini-main">
                    <div class="mini-title">{{ $a->name }}</div>
                    <div class="mini-meta">
                      <span>{{ $a->subs }} submissão{{ $a->subs == 1 ? '' : 'es' }}</span>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          @endif
        </div>

        <div>
          <div class="card-sub-admin mb-2">Carga dos revisores</div>
          @if(($reviewersLoad ?? collect())->isEmpty())
            <div class="empty">Sem dados de revisores neste período.</div>
          @else
            <div class="mini-list">
              @foreach($reviewersLoad as $rv)
                <div class="mini-item">
                  <div class="mini-main">
                    <div class="mini-title">{{ $rv->name ?? '—' }}</div>
                    <div class="pillset mini-meta">
                      <span class="pill pill-soft">Abertas {{ (int)($rv->abertas ?? 0) }}</span>
                      <span class="pill">Concluídas {{ (int)($rv->concluidas ?? 0) }}</span>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
