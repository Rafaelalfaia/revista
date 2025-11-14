@extends('console.layout')
@section('title','Submissões · Admin')
@section('page.title','Submissões')

@push('head')
<style>
  .shell-sub{max-width:1180px;margin:0 auto;padding:1rem;display:flex;flex-direction:column;gap:1rem}
  .flash{border-radius:.85rem;border:1px solid var(--line);background:var(--panel);padding:.55rem .75rem;font-size:.8rem}

  .hero-sub{border-radius:1.3rem;border:1px solid var(--line);overflow:hidden;background:
    radial-gradient(circle at top left,rgba(52,211,153,.16),transparent 55%),
    radial-gradient(circle at top right,rgba(59,130,246,.18),transparent 55%),
    var(--panel)}
  .hero-inner{padding:.9rem 1rem 1.1rem;display:flex;align-items:flex-end;justify-content:space-between;gap:.75rem}
  .hero-main{display:flex;flex-direction:column;gap:.25rem;min-width:0}
  .hero-title{font-size:1rem;font-weight:700}
  .hero-subtext{font-size:.75rem;color:var(--muted)}
  .hero-meta{margin-top:.35rem;font-size:.75rem;color:var(--muted)}
  .hero-actions{display:flex;flex-wrap:wrap;gap:.4rem;justify-content:flex-end;align-items:center}
  .badge-count{border-radius:999px;background:var(--soft);border:1px solid var(--line);padding:.18rem .6rem;font-size:.7rem}
  .hero-kpi{margin-top:.6rem;padding:.5rem .9rem;border-top:1px solid rgba(148,163,184,.4);display:flex;gap:.75rem;font-size:.75rem;flex-wrap:wrap}
  .hero-kpi-item{flex:1;min-width:7rem}
  .hero-kpi-label{color:var(--muted)}
  .hero-kpi-value{font-weight:600;font-size:.95rem}

  .toolbar-sub{display:flex;flex-wrap:wrap;gap:.4rem;align-items:center;justify-content:flex-start;margin-top:.5rem}
  .pill-filter{border-radius:999px;padding:.12rem .55rem;font-size:.72rem;background:var(--soft);border:1px solid var(--line);white-space:nowrap}

  .card-filters{border-radius:1rem;border:1px solid var(--line);background:var(--panel);display:flex;flex-direction:column}
  .card-header{padding:.75rem 1rem;border-bottom:1px solid var(--line);display:flex;align-items:center;justify-content:space-between;gap:.5rem}
  .card-header-title{font-size:.9rem;font-weight:600}
  .card-header-sub{font-size:.75rem;color:var(--muted)}
  .card-body{padding:.75rem 1rem}
  .card-foot{padding:.65rem 1rem;border-top:1px solid var(--line);display:flex;align-items:center;justify-content:space-between;gap:.75rem;flex-wrap:wrap}

  .filter-grid{display:grid;gap:.6rem}
  @media(min-width:640px){.filter-grid{grid-template-columns:repeat(6,minmax(0,1fr))}}
  .filter-input{border-radius:.9rem;border:1px solid var(--line);padding:.5rem .75rem;font-size:.8rem;background:var(--panel);width:100%}
  .filter-range{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.5rem}

  .btn-primary{border-radius:.9rem;border:1px solid transparent;padding:.5rem .95rem;font-size:.8rem;font-weight:600;background:var(--brand);color:#fff;display:inline-flex;align-items:center;gap:.35rem}
  .btn-ghost{border-radius:.9rem;border:1px solid var(--line);padding:.45rem .85rem;font-size:.8rem;font-weight:500;background:var(--panel);display:inline-flex;align-items:center;gap:.35rem}
  .btn-link{font-size:.8rem;color:var(--muted);text-decoration:underline;text-underline-offset:2px}

  .kpi-grid{display:grid;gap:.75rem}
  @media(min-width:640px){.kpi-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
  @media(min-width:1024px){.kpi-grid{grid-template-columns:repeat(4,minmax(0,1fr))}}
  .kpi-card{border-radius:1rem;border:1px solid var(--line);background:var(--panel);padding:.75rem .9rem;display:grid;gap:.15rem}
  .kpi-label{font-size:.75rem;color:var(--muted)}
  .kpi-value{font-size:1.5rem;font-weight:600}
  .kpi-tag{font-size:.7rem;border-radius:999px;padding:.1rem .6rem;background:var(--soft);border:1px solid var(--line);justify-self:flex-start}

  .card-list{border-radius:1rem;border:1px solid var(--line);background:var(--panel);display:flex;flex-direction:column}
  .card-list-header{padding:.75rem 1rem;border-bottom:1px solid var(--line);display:flex;align-items:center;justify-content:space-between;gap:.5rem}
  .card-list-body{padding:.75rem 1rem}

  .submission-list{display:flex;flex-direction:column;gap:.6rem}
  .submission-card{border-radius:1rem;border:1px solid var(--line);background:var(--panel-2);padding:.7rem .85rem;display:flex;align-items:flex-start;justify-content:space-between;gap:.75rem}
  .submission-main{display:flex;align-items:flex-start;gap:.65rem;min-width:0}
  .status-dot{width:.5rem;height:.5rem;border-radius:999px;margin-top:.3rem;flex-shrink:0}
  .submission-text{min-width:0}
  .submission-title{font-size:.9rem;font-weight:600;line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
  .submission-meta{margin-top:.2rem;font-size:.75rem;color:var(--muted);display:flex;flex-wrap:wrap;gap:.35rem}
  .submission-meta span{white-space:nowrap}

  .submission-side{display:flex;flex-direction:column;align-items:flex-end;gap:.35rem;flex-shrink:0}
  .status-pill{border-radius:999px;padding:.15rem .65rem;font-size:.7rem;font-weight:500;display:inline-flex;align-items:center;gap:.3rem;border:1px solid transparent;white-space:nowrap}
  .status-pill-rascunho{background:rgba(148,163,184,.1);color:#64748b;border-color:rgba(148,163,184,.4)}
  .status-pill-submetido{background:rgba(59,130,246,.08);color:#2563eb;border-color:rgba(59,130,246,.35)}
  .status-pill-em-triagem{background:rgba(234,179,8,.08);color:#854d0e;border-color:rgba(234,179,8,.35)}
  .status-pill-em-revisao{background:rgba(59,130,246,.08);color:#1d4ed8;border-color:rgba(59,130,246,.35)}
  .status-pill-revisao-solicitada{background:rgba(249,115,22,.08);color:#c2410c;border-color:rgba(249,115,22,.35)}
  .status-pill-aceito{background:rgba(16,185,129,.08);color:#047857;border-color:rgba(16,185,129,.35)}
  .status-pill-rejeitado{background:rgba(248,113,113,.08);color:#b91c1c;border-color:rgba(248,113,113,.35)}
  .status-pill-publicado{background:rgba(37,99,235,.08);color:#1e3a8a;border-color:rgba(37,99,235,.4)}

  .empty-state{padding:1rem .85rem;text-align:center;font-size:.85rem;color:var(--muted)}

  @media(max-width:767.98px){
    .hero-inner{flex-direction:column;align-items:flex-start}
    .hero-actions{justify-content:flex-start}
  }
</style>
@endpush

@section('content')
<div class="shell-sub">
  @if (session('ok'))
    <div class="flash">{{ session('ok') }}</div>
  @endif

  @php
    $opts = ['rascunho','submetido','em_triagem','em_revisao','revisao_solicitada','aceito','rejeitado','publicado'];
    $df = $dateField ?? 'created_at';
  @endphp

  <div class="hero-sub">
    <div class="hero-inner">
      <div class="hero-main">
        <div class="hero-title">Visão geral de submissões</div>
        <div class="hero-subtext">Administre o fluxo editorial da revista em uma lista única, filtrável e pronta para tela cheia.</div>
        <div class="hero-meta">
          Intervalo atual:
          @if($from && $to)
            {{ optional($from)->format('d/m/Y') }} – {{ optional($to)->format('d/m/Y') }}
          @else
            sem limite definido
          @endif
        </div>
      </div>
      <div class="hero-actions">
        <span class="badge-count">{{ $rows->total() }} resultado{{ $rows->total() === 1 ? '' : 's' }}</span>
      </div>
    </div>
    <div class="hero-kpi">
      <div class="hero-kpi-item">
        <div class="hero-kpi-label">Submetidos</div>
        <div class="hero-kpi-value">{{ $stats['submetido'] ?? 0 }}</div>
      </div>
      <div class="hero-kpi-item">
        <div class="hero-kpi-label">Em triagem</div>
        <div class="hero-kpi-value">{{ $stats['em_triagem'] ?? 0 }}</div>
      </div>
      <div class="hero-kpi-item">
        <div class="hero-kpi-label">Em revisão</div>
        <div class="hero-kpi-value">{{ $stats['em_revisao'] ?? 0 }}</div>
      </div>
      <div class="hero-kpi-item">
        <div class="hero-kpi-label">Correções solicitadas</div>
        <div class="hero-kpi-value">{{ $stats['revisao_solicitada'] ?? 0 }}</div>
      </div>
    </div>
    <div class="toolbar-sub">
      @if($q)
        <span class="pill-filter">Busca: “{{ \Illuminate\Support\Str::limit($q,30) }}”</span>
      @endif
      @if($status)
        <span class="pill-filter">Status: {{ str_replace('_',' ',$status) }}</span>
      @endif
      @if($authorId)
        <span class="pill-filter">Autor filtrado</span>
      @endif
      @if($categoryId)
        <span class="pill-filter">Categoria filtrada</span>
      @endif
    </div>
  </div>

  <form method="GET" class="card-filters">
    <div class="card-header">
      <div>
        <div class="card-header-title">Filtros</div>
        <div class="card-header-sub">Refine as submissões por status, autor, categoria e período.</div>
      </div>
    </div>
    <div class="card-body">
      <div class="filter-grid">
        <div class="sm:col-span-2">
          <input name="q" value="{{ $q }}" placeholder="Buscar por título ou slug" class="filter-input">
        </div>

        <select name="status" class="filter-input">
          <option value="">Todos status</option>
          @foreach($opts as $opt)
            <option value="{{ $opt }}" @selected($status===$opt)>{{ str_replace('_',' ',$opt) }}</option>
          @endforeach
        </select>

        <select name="author_id" class="filter-input">
          <option value="">Todos autores</option>
          @foreach($authors as $a)
            <option value="{{ $a->id }}" @selected(($authorId ?? null)===$a->id)>{{ $a->name }}</option>
          @endforeach
        </select>

        <select name="category_id" class="filter-input">
          <option value="">Todas categorias</option>
          @foreach($categories as $c)
            <option value="{{ $c->id }}" @selected(($categoryId ?? null)===$c->id)>{{ $c->name }}</option>
          @endforeach
        </select>

        <select name="date_field" class="filter-input">
          <option value="created_at"   @selected($df==='created_at')>Criada em</option>
          <option value="submitted_at" @selected($df==='submitted_at')>Submetida em</option>
        </select>

        <div class="filter-range">
          <input type="date" name="from" value="{{ optional($from)->format('Y-m-d') }}" class="filter-input">
          <input type="date" name="to"   value="{{ optional($to)->format('Y-m-d')   }}" class="filter-input">
        </div>
      </div>
    </div>
    <div class="card-foot">
      <div class="flex items-center gap-2">
        <button class="btn-primary" type="submit">
          <span>Filtrar</span>
        </button>
        <a href="{{ route('admin.submissions.index') }}" class="btn-link">Limpar filtros</a>
      </div>
      <div class="text-xs text-right text-[color:var(--muted)]">
        {{ $rows->total() }} resultados
      </div>
    </div>
  </form>

  <div class="kpi-grid">
    @foreach (['submetido','em_triagem','em_revisao','revisao_solicitada'] as $k)
      @php $label = str_replace('_',' ',$k); @endphp
      <div class="kpi-card">
        <div class="kpi-label">Submissões {{ $label }}</div>
        <div class="kpi-value">{{ $stats[$k] ?? 0 }}</div>
        <div class="kpi-tag">{{ ucfirst($label) }}</div>
      </div>
    @endforeach
  </div>

  <div class="card-list">
    <div class="card-list-header">
      <div>
        <div class="card-header-title">Lista de submissões</div>
        <div class="card-header-sub">Visualização em cards, otimizada para uso em tela cheia e dispositivos móveis.</div>
      </div>
      <span class="badge-count">{{ $rows->count() }} na página</span>
    </div>
    <div class="card-list-body">
      @forelse ($rows as $s)
        @php
          $statusKey = str_replace('_','-',$s->status ?? 'indefinido');
          $statusLabel = ucwords(str_replace('_',' ',$s->status ?? 'indefinido'));
          $authorName = $s->author?->name ?? 'Autor não definido';
          $created = $s->created_at?->format('d/m/Y H:i');
        @endphp
        <div class="submission-card">
          <div class="submission-main">
            <div class="status-dot
              @if($statusKey==='submetido' || $statusKey==='em-revisao') bg-blue-500
              @elseif($statusKey==='aceito') bg-emerald-500
              @elseif($statusKey==='rejeitado') bg-rose-500
              @elseif($statusKey==='publicado') bg-indigo-500
              @elseif($statusKey==='em-triagem') bg-amber-400
              @else bg-slate-400
              @endif"></div>
            <div class="submission-text">
              <div class="submission-title">{{ $s->title }}</div>
              <div class="submission-meta">
                <span>#{{ $s->id }}</span>
                @if($created)
                  <span>· Criada em {{ $created }}</span>
                @endif
                <span>· {{ $authorName }}</span>
              </div>
            </div>
          </div>
          <div class="submission-side">
            <span class="status-pill status-pill-{{ $statusKey }}">{{ $statusLabel }}</span>
            <a href="{{ route('admin.submissions.show',$s) }}" class="btn-ghost text-xs">
              Abrir
            </a>
          </div>
        </div>
      @empty
        <div class="empty-state">Nenhuma submissão encontrada com os filtros atuais.</div>
      @endforelse
    </div>
  </div>

  <div class="mt-4">
    {{ $rows->links() }}
  </div>
</div>
@endsection
