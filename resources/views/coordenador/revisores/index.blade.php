@extends('console.layout')

@section('title','Revisores · Coordenador')
@section('page.title','Revisores')

@push('head')
<style>
  .shell-revisores{display:flex;flex-direction:column;gap:1rem}
  .alert-ok,.alert-err{border-radius:1rem;border:1px solid var(--line);padding:.6rem .8rem;font-size:.8rem}
  .alert-ok{background:var(--panel);color:var(--text)}
  .alert-err{background:rgba(248,113,113,.08);color:rgb(248,113,113);border-color:rgba(248,113,113,.6)}

  .hero-card{border-radius:1.3rem;border:1px solid var(--line);background:
    radial-gradient(circle at top left,rgba(52,211,153,.16),transparent 55%),
    radial-gradient(circle at top right,rgba(59,130,246,.18),transparent 55%),
    var(--panel);padding:.85rem 1rem;display:flex;flex-direction:column;gap:.5rem}
  .hero-top{display:flex;justify-content:space-between;align-items:flex-start;gap:.75rem}
  .hero-main{min-width:0}
  .hero-label{font-size:.7rem;color:var(--muted);text-transform:uppercase;letter-spacing:.08em}
  .hero-title{font-size:.95rem;font-weight:700;line-height:1.3}
  .hero-meta{margin-top:.15rem;font-size:.75rem;color:var(--muted)}
  .hero-actions{display:flex;flex-wrap:wrap;gap:.4rem}
  .btn-brand{border-radius:.9rem;padding:.45rem .9rem;font-size:.8rem;font-weight:600;display:inline-flex;align-items:center;gap:.3rem;background:var(--brand);color:#fff;border:none;white-space:nowrap}
  .badge-count{border-radius:999px;padding:.18rem .6rem;font-size:.7rem;border:1px solid var(--line);background:var(--panel-2)}

  .filter-card{border-radius:1.1rem;border:1px solid var(--line);background:var(--panel);padding:.7rem .85rem;display:flex;flex-direction:column;gap:.6rem}
  .filter-header{display:flex;justify-content:space-between;align-items:center;gap:.5rem}
  .filter-title{font-size:.85rem;font-weight:600}
  .filter-meta{font-size:.75rem;color:var(--muted)}
  .filter-body{display:grid;gap:.45rem}
  @media(min-width:640px){.filter-body{grid-template-columns:minmax(0,2fr) auto}}
  .input-shell{position:relative;display:flex;align-items:center}
  .input-shell input{width:100%;border-radius:.85rem;border:1px solid var(--line);background:var(--panel-2);padding:.55rem .75rem;font-size:.8rem;padding-left:2rem}
  .input-shell span.icon{position:absolute;left:.55rem;font-size:.9rem;color:var(--muted)}
  .btn-clear{border-radius:.85rem;border:1px solid var(--line);background:var(--panel-2);padding:.45rem .8rem;font-size:.75rem;font-weight:500;white-space:nowrap}

  .list-shell{display:flex;flex-direction:column;gap:.6rem}
  .list-empty{border-radius:1.1rem;border:1px dashed var(--line);padding:1.1rem .9rem;text-align:center;font-size:.8rem;color:var(--muted)}
  .card-reviewer{border-radius:1.1rem;border:1px solid var(--line);background:var(--panel);padding:.65rem .8rem;display:grid;gap:.35rem}
  .rev-header{display:flex;justify-content:space-between;gap:.5rem;align-items:flex-start}
  .rev-main{min-width:0}
  .rev-name{font-size:.9rem;font-weight:600;line-height:1.3}
  .rev-meta{margin-top:.15rem;font-size:.75rem;color:var(--muted);display:flex;flex-wrap:wrap;gap:.35rem}
  .rev-meta span{display:inline-flex;align-items:center;gap:.18rem}
  .rev-tags{display:flex;flex-wrap:wrap;gap:.25rem;margin-top:.15rem}
  .tag-area{border-radius:.85rem;padding:.16rem .55rem;font-size:.7rem;border:1px solid var(--line);background:var(--panel-2)}
  .tag-empty{font-size:.7rem;color:var(--muted)}

  .rev-actions{display:flex;flex-wrap:wrap;gap:.35rem;justify-content:flex-end}
  .btn-outline{border-radius:.8rem;border:1px solid var(--line);background:var(--panel-2);padding:.32rem .75rem;font-size:.75rem;font-weight:500;display:inline-flex;align-items:center;gap:.25rem;white-space:nowrap}
  .btn-danger{border-radius:.8rem;border:1px solid rgba(248,113,113,.7);background:rgba(248,113,113,.04);padding:.32rem .75rem;font-size:.75rem;font-weight:500;display:inline-flex;align-items:center;gap:.25rem;color:rgb(248,113,113);white-space:nowrap}

  .pagination-shell{margin-top:1rem}

  @media(max-width:639.98px){
    .hero-top{flex-direction:column-reverse}
    .hero-actions{justify-content:flex-start}
  }
</style>
@endpush

@section('content')
<div class="shell-revisores">
  @if (session('ok'))
    <div class="alert-ok">{{ session('ok') }}</div>
  @endif
  @if (session('err'))
    <div class="alert-err">{{ session('err') }}</div>
  @endif

  <div class="hero-card">
    <div class="hero-top">
      <div class="hero-main">
        <div class="hero-label">Equipe</div>
        <div class="hero-title">Revisores sob sua coordenação</div>
        <div class="hero-meta">
          Gerencie quem recebe e analisa as submissões da sua área.
        </div>
      </div>
      <div class="hero-actions">
        <span class="badge-count">
          {{ $revisores->total() }} revisor{{ $revisores->total() === 1 ? '' : 'es' }}
        </span>
        @can('reviewers.manage')
          <a href="{{ route('coordenador.revisores.create') }}" class="btn-brand">
            <span>+ Novo revisor</span>
          </a>
        @endcan
      </div>
    </div>
  </div>

  <form method="GET" class="filter-card">
    <div class="filter-header">
      <div>
        <div class="filter-title">Filtrar revisores</div>
        <div class="filter-meta">
          Busque por nome, e-mail ou CPF.
        </div>
      </div>
    </div>
    <div class="filter-body">
      <div class="input-shell">
        <input type="search" name="q" value="{{ $q }}" placeholder="Digite para buscar revisores">
      </div>
      <button type="submit" class="btn-clear">
        Aplicar filtro
      </button>
    </div>
  </form>

  <div class="list-shell">
    @forelse ($revisores as $u)
      <div class="card-reviewer">
        <div class="rev-header">
          <div class="rev-main">
            <div class="rev-name">{{ $u->name }}</div>
            <div class="rev-meta">
              <span>
                <span>E-mail:</span>
                <span>{{ $u->email ?: '—' }}</span>
              </span>
              <span>
                <span>CPF:</span>
                <span>{{ $u->cpf_formatted ?? $u->cpf ?? '—' }}</span>
              </span>
            </div>
            <div class="rev-tags">
              @forelse ($u->categories as $cat)
                <span class="tag-area">{{ $cat->name }}</span>
              @empty
                <span class="tag-empty">Sem áreas atribuídas</span>
              @endforelse
            </div>
          </div>
        </div>
        <div class="rev-actions">
          <a href="{{ route('coordenador.revisores.edit', $u) }}" class="btn-outline">
            <span>Editar</span>
          </a>

          <form action="{{ route('coordenador.revisores.destroy', $u) }}" method="POST" onsubmit="return confirm('Excluir este revisor? Esta ação não pode ser desfeita.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-danger">
              <span>Excluir</span>
            </button>
          </form>
        </div>
      </div>
    @empty
      <div class="list-empty">
        Nenhum revisor encontrado. Crie um novo revisor ou ajuste o filtro de busca.
      </div>
    @endforelse
  </div>

  <div class="pagination-shell">
    {{ $revisores->links() }}
  </div>
</div>
@endsection
