@extends('console.layout')

@section('title','Leitura — Submissão #'.$submission->id)
@section('page.title','Leitura da Submissão')

@php
  $statusLabels = [
    'rascunho'            => 'Rascunho',
    'submetido'          => 'Submetido',
    'em_triagem'         => 'Em triagem',
    'em_revisao'         => 'Em revisão',
    'revisao_solicitada' => 'Revisão solicitada',
    'aceito'             => 'Aceito',
    'rejeitado'          => 'Rejeitado',
    'publicado'          => 'Publicado',
  ];

  $statusRaw   = $submission->status ?? 'indefinido';
  $statusKey   = str_replace('_','-', $statusRaw);
  $statusLabel = $statusLabels[$statusRaw] ?? ucwords(str_replace('_',' ', $statusRaw));

  $autorNome     = $submission->user->name ?? $submission->author_name ?? null;
  $categoriaNome = $submission->category->name ?? null;

  $reviewersList = isset($reviewers) && is_array($reviewers) && count($reviewers)
    ? collect($reviewers)
    : ($submission->reviews ?? collect())->pluck('reviewer.name')->filter();
  $reviewersText = $reviewersList->count() ? $reviewersList->join(', ') : null;

  if (isset($roots)) {
    $rootsLocal = $roots;
  } elseif (method_exists($submission, 'sectionsRoot')) {
    $rootsLocal = $submission->sectionsRoot;
  } elseif (method_exists($submission, 'sections')) {
    $rootsLocal = $submission->sections;
  } else {
    $rootsLocal = collect();
  }

  if (is_array($rootsLocal)) {
    $rootsLocal = collect($rootsLocal);
  }

  $bodyHtmlFallback = $submission->body_html
    ?? $submission->content_html
    ?? $submission->content
    ?? $submission->body
    ?? null;
@endphp

@push('head')
<style>
  .reader-shell{display:flex;flex-direction:column;gap:.85rem}
  .reader-header-card{border-radius:1.3rem;border:1px solid var(--line);background:
    radial-gradient(circle at top left,rgba(52,211,153,.16),transparent 55%),
    radial-gradient(circle at top right,rgba(59,130,246,.18),transparent 55%),
    var(--panel);padding:.85rem 1rem}
  .reader-header-top{display:flex;justify-content:space-between;align-items:flex-start;gap:.75rem}
  .reader-title-block{min-width:0}
  .reader-label{font-size:.7rem;color:var(--muted);text-transform:uppercase;letter-spacing:.08em}
  .reader-title{font-size:.95rem;font-weight:700;line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
  .reader-meta{margin-top:.2rem;font-size:.75rem;color:var(--muted);display:flex;flex-wrap:wrap;gap:.35rem}
  .reader-meta span{display:inline-flex;align-items:center;gap:.2rem}
  .reader-toolbar{display:flex;flex-wrap:wrap;gap:.35rem;justify-content:flex-end}
  .status-pill{border-radius:999px;padding:.2rem .7rem;font-size:.7rem;font-weight:500;border:1px solid transparent;white-space:nowrap}
  .status-pill.st-rascunho{background:rgba(148,163,184,.12);color:#475569;border-color:rgba(148,163,184,.4)}
  .status-pill.st-submetido{background:rgba(59,130,246,.12);color:#1d4ed8;border-color:rgba(59,130,246,.35)}
  .status-pill.st-em-triagem{background:rgba(234,179,8,.12);color:#854d0e;border-color:rgba(234,179,8,.4)}
  .status-pill.st-em-revisao{background:rgba(59,130,246,.12);color:#1d4ed8;border-color:rgba(59,130,246,.4)}
  .status-pill.st-revisao-solicitada{background:rgba(249,115,22,.12);color:#c2410c;border-color:rgba(249,115,22,.4)}
  .status-pill.st-aceito{background:rgba(16,185,129,.14);color:#047857;border-color:rgba(16,185,129,.5)}
  .status-pill.st-rejeitado{background:rgba(248,113,113,.12);color:#b91c1c;border-color:rgba(248,113,113,.45)}
  .status-pill.st-publicado{background:rgba(37,99,235,.14);color:#1e3a8a;border-color:rgba(37,99,235,.5)}
  .status-pill.st-indefinido{background:rgba(148,163,184,.12);color:#475569;border-color:rgba(148,163,184,.4)}
  .reader-header-bottom{margin-top:.55rem;display:flex;flex-wrap:wrap;gap:.5rem;font-size:.75rem;color:var(--muted)}
  .reader-header-bottom span{display:inline-flex;align-items:center;gap:.2rem}

  .reader-main-card{border-radius:1.3rem;border:1px solid var(--line);background:var(--panel);overflow:hidden;display:flex;flex-direction:column}
  .sub-toolbar{padding:.55rem .8rem;border-bottom:1px solid var(--line);display:flex;justify-content:space-between;align-items:center;gap:.5rem;font-size:.75rem}
  .sub-toolbar-left{display:flex;align-items:center;gap:.4rem;color:var(--muted)}
  .sub-toolbar-right{display:flex;flex-wrap:wrap;gap:.3rem}
  .btn-ghost{border-radius:.8rem;border:1px solid transparent;background:transparent;padding:.25rem .6rem;font-size:.75rem;display:inline-flex;align-items:center;gap:.2rem;color:var(--muted)}
  .btn-ghost-active{border-color:var(--line);background:var(--panel-2);color:var(--text)}
  .reader-scroll{max-height:calc(100vh - 220px);overflow:auto}
  .readable{column-gap:2.1rem}
  .reader-single .readable{column-count:1}
  .reader-dual .readable{column-count:2}
  .section h2,.section h3{font-weight:800;line-height:1.25;margin:0 0 .5rem}
  .section h2{font-size:1.25rem}
  .section h3{font-size:1.05rem}
  .empty-reader{padding:1rem .9rem;font-size:.8rem;color:var(--muted);text-align:center}

  @media(max-width:767.98px){
    .reader-header-top{flex-direction:column-reverse}
    .reader-toolbar{justify-content:flex-start}
    .reader-scroll{max-height:none}
    .reader-main-card{border-radius:1.1rem}
  }
</style>
@endpush

@section('content')
<div x-data="{ columns: 2, size: 17 }" class="reader-shell">
  <div class="reader-header-card">
    <div class="reader-header-top">
      <div class="reader-title-block">
        <div class="reader-label">Submissão #{{ $submission->id }}</div>
        <div class="reader-title" title="{{ $submission->title }}">{{ $submission->title }}</div>
        <div class="reader-meta">
          @if($autorNome)
            <span><span>Autor:</span><span>{{ $autorNome }}</span></span>
          @endif
          @if($categoriaNome)
            <span><span>Categoria:</span><span>{{ $categoriaNome }}</span></span>
          @endif
          @if($reviewersText)
            <span><span>Revisor(es):</span><span>{{ $reviewersText }}</span></span>
          @endif
        </div>
      </div>
      <div class="reader-toolbar">
        <span class="status-pill st-{{ $statusKey }}">{{ $statusLabel }}</span>
      </div>
    </div>

    <div class="reader-header-bottom">
      @if($submission->submitted_at)
        <span><span>Submetido em</span><span>{{ $submission->submitted_at->format('d/m/Y H:i') }}</span></span>
      @endif
      @if($submission->updated_at)
        <span><span>Última atualização</span><span>{{ $submission->updated_at->format('d/m/Y H:i') }}</span></span>
      @endif
    </div>
  </div>

  <div class="reader-main-card" :class="columns === 2 ? 'reader-dual' : 'reader-single'">
    <div class="sub-toolbar">
      <div class="sub-toolbar-left">
        <span>Visualização</span>
      </div>
      <div class="sub-toolbar-right">
        <button type="button"
                class="btn-ghost"
                :class="columns === 1 ? 'btn-ghost-active' : ''"
                @click="columns = 1">
          <span>1 col</span>
        </button>
        <button type="button"
                class="btn-ghost"
                :class="columns === 2 ? 'btn-ghost-active' : ''"
                @click="columns = 2">
          <span>2 col</span>
        </button>
        <button type="button"
                class="btn-ghost"
                @click="size = Math.max(15, size - 1)">
          <span>A−</span>
        </button>
        <button type="button"
                class="btn-ghost"
                @click="size = Math.min(22, size + 1)">
          <span>A+</span>
        </button>
      </div>
    </div>

    <div class="reader-scroll">
      @if($rootsLocal instanceof \Illuminate\Support\Collection ? $rootsLocal->isNotEmpty() : !empty($rootsLocal))
        <div class="readable p-4 md:p-6" :style="`font-size:${size}px;line-height:1.7`">
          @foreach ($rootsLocal as $sec)
            <article class="section break-inside-avoid mb-6">
              @if($sec->title || $sec->label)
                <h2>{{ trim(($sec->label ? $sec->label.' ' : '').($sec->title ?? '')) }}</h2>
              @endif
              @if(!empty($sec->content_html) || !empty($sec->content) || !empty($sec->body))
                <div class="prose prose-sm max-w-none dark:prose-invert">
                  {!! $sec->content_html ?? $sec->content ?? $sec->body !!}
                </div>
              @endif

              @if($sec->children && $sec->children->count())
                @foreach($sec->children as $ch)
                  <section class="mt-5 break-inside-avoid">
                    @if($ch->title || $ch->label)
                      <h3>{{ trim(($ch->label ? $ch->label.' ' : '').($ch->title ?? '')) }}</h3>
                    @endif
                    @if(!empty($ch->content_html) || !empty($ch->content) || !empty($ch->body))
                      <div class="prose prose-sm max-w-none dark:prose-invert">
                        {!! $ch->content_html ?? $ch->content ?? $ch->body !!}
                      </div>
                    @endif
                  </section>
                @endforeach
              @endif
            </article>
          @endforeach
        </div>
      @elseif($bodyHtmlFallback)
        <div class="readable p-4 md:p-6 reader-single" :style="`font-size:${size}px;line-height:1.7`">
          <article class="section break-inside-avoid mb-6">
            <div class="prose prose-sm max-w-none dark:prose-invert">
              {!! $bodyHtmlFallback !!}
            </div>
          </article>
        </div>
      @else
        <div class="empty-reader">
          Esta submissão ainda não possui conteúdo estruturado disponível para leitura.
        </div>
      @endif
    </div>
  </div>
</div>
@endsection
