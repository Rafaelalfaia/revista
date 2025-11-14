@extends('console.layout')
@section('title','Minhas revisões')
@section('page.title','Minhas revisões')

@push('head')
<style>
  .rev-list-shell{
    max-width:1100px;
    margin:0 auto;
    padding:.75rem 0 0;
    display:flex;
    flex-direction:column;
    gap:1rem;
  }

  .rev-filter-card{
    border:1px solid var(--line);
    background:var(--panel);
    border-radius:1.1rem;
    padding:.75rem .9rem;
  }
  .rev-filter-title{
    font-size:.8rem;
    font-weight:500;
    margin-bottom:.45rem;
  }
  .rev-filter-grid{
    display:grid;
    gap:.5rem;
    grid-template-columns:minmax(0,1fr);
  }
  @media(min-width:640px){
    .rev-filter-grid{
      grid-template-columns:minmax(0,1.2fr) 180px auto auto;
      align-items:center;
    }
  }
  .rev-input{
    width:100%;
    height:2.5rem;
    border-radius:.75rem;
    border:1px solid var(--line);
    background:var(--panel-2);
    padding:0 .75rem;
    font-size:.85rem;
  }
  .rev-input:focus{
    outline:none;
    box-shadow:0 0 0 1px var(--brand);
  }
  .rev-btn{
    border-radius:.75rem;
    border:1px solid var(--line);
    background:var(--panel-2);
    height:2.5rem;
    padding:0 .9rem;
    font-size:.8rem;
    font-weight:500;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    white-space:nowrap;
  }
  .rev-btn-primary{
    background:var(--brand);
    color:#fff;
    border-color:transparent;
  }

  .rev-meta{
    font-size:.78rem;
    color:var(--muted);
  }

  .rev-card{
    border:1px solid var(--line);
    background:var(--panel);
    border-radius:1.1rem;
    padding:.75rem .85rem .85rem;
  }
  .rev-card-top{
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap:.75rem;
  }
  .rev-card-title{
    font-size:.9rem;
    font-weight:500;
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
  }
  .rev-card-meta{
    font-size:.78rem;
    color:var(--muted);
    margin-top:.15rem;
  }
  .rev-pill-row{
    display:flex;
    flex-wrap:wrap;
    gap:.35rem;
    justify-content:flex-end;
    margin-left:.5rem;
  }
  .rev-pill{
    border-radius:999px;
    padding:.18rem .65rem;
    font-size:.7rem;
    white-space:nowrap;
  }
  .rev-pill-danger{
    background:rgba(244,63,94,.18);
    color:var(--text);
  }

  .rev-chip{
    border-radius:999px;
    padding:.15rem .65rem;
    font-size:.7rem;
    background:var(--chip);
    white-space:nowrap;
  }

  .rev-card-footer{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:.5rem;
    margin-top:.7rem;
  }
  .rev-card-cat{
    font-size:.75rem;
    color:var(--muted);
  }
  .rev-card-action{
    border-radius:.75rem;
    border:1px solid var(--line);
    background:var(--panel-2);
    height:2.25rem;
    padding:0 .85rem;
    font-size:.8rem;
    display:inline-flex;
    align-items:center;
    justify-content:center;
  }

  .rev-empty{
    font-size:.85rem;
    color:var(--muted);
    padding:1.25rem;
    border-radius:1rem;
    border:1px dashed var(--line);
    background:var(--panel);
    text-align:center;
  }

  .rev-table-shell{
    border:1px solid var(--line);
    background:var(--panel);
    border-radius:1.1rem;
    overflow:hidden;
  }
  .rev-table-head{
    background:var(--panel-2);
  }
  .rev-table-head th{
    font-size:.75rem;
    text-transform:uppercase;
    letter-spacing:.06em;
    color:var(--muted);
  }
  .rev-table-cell{
    padding:.6rem .75rem;
    font-size:.85rem;
  }
</style>
@endpush

@section('content')
@php
  $q      = $q      ?? request('q','');
  $status = $status ?? request('status');

  $reviewStatusMap = [
    'atribuida'          => 'Atribuída',
    'em_revisao'         => 'Em revisão',
    'revisao_solicitada' => 'Correções solicitadas',
    'parecer_enviado'    => 'Parecer enviado',
  ];

  $submissionStatusMap = [
    'rascunho'            => 'Rascunho',
    'submetido'           => 'Submetido',
    'em_triagem'          => 'Em triagem',
    'em_revisao'          => 'Em revisão',
    'revisao_solicitada'  => 'Correções solicitadas',
    'aceito'              => 'Aceito',
    'rejeitado'           => 'Rejeitado',
    'publicado'           => 'Publicado',
  ];

  $statusColor = function(string $s){
    return match($s){
      'em_revisao'         => ['bg'=>'var(--brand)','tx'=>'#fff'],
      'revisao_solicitada' => ['bg'=>'rgba(234,179,8,.18)','tx'=>'var(--text)'],
      'submetido'          => ['bg'=>'rgba(59,130,246,.18)','tx'=>'var(--text)'],
      'em_triagem'         => ['bg'=>'rgba(147,197,253,.18)','tx'=>'var(--text)'],
      'aceito'             => ['bg'=>'rgba(16,185,129,.18)','tx'=>'var(--text)'],
      'rejeitado'          => ['bg'=>'rgba(244,63,94,.18)','tx'=>'var(--text)'],
      'publicado'          => ['bg'=>'rgba(34,197,94,.18)','tx'=>'var(--text)'],
      default              => ['bg'=>'var(--chip)','tx'=>'var(--text)'],
    };
  };
@endphp

<div class="rev-list-shell">

  @if (session('ok'))
    <div class="rounded-xl border panel p-3 text-sm"
         style="border-color:var(--line)">
      {{ session('ok') }}
    </div>
  @endif

  <form method="GET" class="rev-filter-card">
    <div class="rev-filter-title">Filtros</div>
    <div class="rev-filter-grid">
      <input id="q" type="search" name="q" value="{{ $q }}"
             placeholder="Buscar por título da submissão"
             class="rev-input">

      <select id="status" name="status" class="rev-input">
        <option value="">Todos os status de revisão</option>
        @foreach ($reviewStatusMap as $k => $v)
          <option value="{{ $k }}" @selected($status === $k)>{{ $v }}</option>
        @endforeach
      </select>

      <button class="rev-btn rev-btn-primary" type="submit">
        Aplicar
      </button>

      @if(strlen($q) || strlen($status))
        <a href="{{ route('revisor.reviews.index') }}" class="rev-btn">
          Limpar
        </a>
      @endif
    </div>
  </form>

  @if(method_exists($reviews,'count') && $reviews->count())
    <div class="rev-meta">
      Mostrando {{ $reviews->firstItem() }}–{{ $reviews->lastItem() }} de {{ $reviews->total() }} resultados.
    </div>
  @endif

  <div class="md:hidden space-y-3">
    @forelse ($reviews as $rv)
      @php
        $sub   = $rv->submission;
        $ss    = $sub->status ?? '—';
        $submissionLabel = $submissionStatusMap[$ss] ?? ucfirst(str_replace('_',' ',$ss));
        $reviewLabel     = $reviewStatusMap[$rv->status] ?? ucfirst(str_replace('_',' ',$rv->status));
        $cat   = optional($sub->categories->first())->name;
        $pend  = $blockingCounts[$rv->submission_id] ?? 0;
        ['bg'=>$bg,'tx'=>$tx] = $statusColor($ss);
        $title = $sub->title ?? '—';
      @endphp
      <div class="rev-card">
        <div class="rev-card-top">
          <div class="min-w-0">
            <div class="rev-card-title" title="{{ $title }}">{{ $title }}</div>
            <div class="rev-card-meta">
              Atualizado {{ $rv->updated_at?->diffForHumans() }}
            </div>
          </div>
          <div class="rev-pill-row">
            @if($pend > 0)
              <span class="rev-pill rev-pill-danger">
                {{ $pend }} pendência(s)
              </span>
            @endif
            <span class="rev-pill" style="background:{{ $bg }};color:{{ $tx }}">
              {{ $submissionLabel }}
            </span>
            <span class="rev-pill rev-chip">
              Revisão: {{ $reviewLabel }}
            </span>
          </div>
        </div>

        <div class="rev-card-footer">
          <div class="rev-card-cat">
            @if($cat)
              Categoria: {{ $cat }}
            @else
              Sem categoria
            @endif
          </div>
          <a href="{{ route('revisor.reviews.show', $rv) }}" class="rev-card-action">
            Abrir
          </a>
        </div>
      </div>
    @empty
      <div class="rev-empty">
        Nenhuma revisão encontrada.
      </div>
    @endforelse
  </div>

  <div class="hidden md:block overflow-x-auto rev-table-shell">
    <table class="min-w-full text-sm">
      <thead class="rev-table-head">
        <tr>
          <th class="rev-table-cell text-left">Título</th>
          <th class="rev-table-cell text-left">Categoria</th>
          <th class="rev-table-cell text-left">Pendências</th>
          <th class="rev-table-cell text-left">Submissão</th>
          <th class="rev-table-cell text-left">Revisão</th>
          <th class="rev-table-cell text-left">Atualizado</th>
          <th class="rev-table-cell text-right">Ações</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($reviews as $rv)
          @php
            $sub   = $rv->submission;
            $ss    = $sub->status ?? '—';
            $submissionLabel = $submissionStatusMap[$ss] ?? ucfirst(str_replace('_',' ',$ss));
            $reviewLabel     = $reviewStatusMap[$rv->status] ?? ucfirst(str_replace('_',' ',$rv->status));
            ['bg'=>$bg,'tx'=>$tx] = $statusColor($ss);
            $title = $sub->title ?? '—';
            $cat   = optional($sub->categories->first())->name;
            $pend  = $blockingCounts[$rv->submission_id] ?? 0;
          @endphp
          <tr class="border-t" style="border-color:var(--line)">
            <td class="rev-table-cell max-w-[28rem] truncate" title="{{ $title }}">
              {{ $title }}
            </td>
            <td class="rev-table-cell">
              {{ $cat ?? '—' }}
            </td>
            <td class="rev-table-cell">
              @if($pend>0)
                <span class="rev-pill rev-pill-danger">{{ $pend }}</span>
              @else
                <span class="rev-pill rev-chip">0</span>
              @endif
            </td>
            <td class="rev-table-cell">
              <span class="rev-pill" style="background:{{ $bg }};color:{{ $tx }}">
                {{ $submissionLabel }}
              </span>
            </td>
            <td class="rev-table-cell">
              <span class="rev-pill rev-chip">
                {{ $reviewLabel }}
              </span>
            </td>
            <td class="rev-table-cell" title="{{ $rv->updated_at?->format('d/m/Y H:i') }}">
              {{ $rv->updated_at?->diffForHumans() }}
            </td>
            <td class="rev-table-cell text-right">
              <a href="{{ route('revisor.reviews.show', $rv) }}"
                 class="rev-card-action">
                Abrir
              </a>
            </td>
          </tr>
        @empty
          <tr>
            <td class="rev-table-cell text-center muted" colspan="7">
              Nenhuma revisão encontrada.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">
    {{ $reviews->withQueryString()->links() }}
  </div>
</div>
@endsection
