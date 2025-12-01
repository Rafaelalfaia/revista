@extends('site.layout')

@section('title', ($submission->title ?? 'Publicação').' · Revista Trivento')

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
  $editionTitle  = $submission->edition->title ?? null;

  // Site mostra apenas um revisor
  if (isset($reviewers) && is_array($reviewers) && count($reviewers)) {
      $reviewerName = collect($reviewers)->first();
  } elseif ($submission->reviews ?? null) {
      $reviewerName = optional(($submission->reviews)->first())->reviewer->name ?? null;
  } else {
      $reviewerName = null;
  }

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

  $publishedDate = $submission->published_at
      ?? $submission->submitted_at
      ?? $submission->created_at;
@endphp

@push('head')
<style>
  .article-shell{max-width:56rem;margin:0 auto;padding:1.5rem 1.25rem 2.5rem}
  @media(min-width:768px){
    .article-shell{padding:2.5rem 0 3.5rem}
  }

  .reader-shell{display:flex;flex-direction:column;gap:.85rem}

  .reader-header-card{
    border-radius:1.3rem;
    border:1px solid var(--line);
    background:
      radial-gradient(circle at top left,rgba(244,114,182,.20),transparent 55%),
      radial-gradient(circle at top right,rgba(236,72,153,.18),transparent 55%),
      var(--panel);
    padding:1.1rem 1.2rem;
  }

  .reader-header-top{display:flex;justify-content:space-between;align-items:flex-start;gap:1rem}
  .reader-title-block{min-width:0}
  .reader-label{font-size:.7rem;color:var(--muted);text-transform:uppercase;letter-spacing:.08em}
  .reader-title{font-size:1.25rem;font-weight:800;line-height:1.35;display:block}
  @media(min-width:768px){
    .reader-title{font-size:1.6rem}
  }
  .reader-meta{margin-top:.4rem;font-size:.8rem;color:var(--muted);display:flex;flex-wrap:wrap;gap:.35rem}
  .reader-meta span{display:inline-flex;align-items:center;gap:.2rem}

  .status-pill{
    border-radius:999px;
    padding:.25rem .8rem;
    font-size:.7rem;
    font-weight:500;
    border:1px solid rgba(236,72,153,.35);
    background:rgba(236,72,153,.12);
    color:#be185d;
    white-space:nowrap;
  }

  .reader-header-bottom{margin-top:.65rem;display:flex;flex-wrap:wrap;gap:.5rem;font-size:.75rem;color:var(--muted)}
  .reader-header-bottom span{display:inline-flex;align-items:center;gap:.2rem}

  .reader-main-card{border-radius:1.3rem;border:1px solid var(--line);background:var(--panel);overflow:hidden;display:flex;flex-direction:column;margin-top:.75rem}
  .sub-toolbar{padding:.55rem .9rem;border-bottom:1px solid var(--line);display:flex;justify-content:space-between;align-items:center;gap:.5rem;font-size:.75rem}
  .sub-toolbar-left{display:flex;align-items:center;gap:.4rem;color:var(--muted)}
  .sub-toolbar-right{display:flex;flex-wrap:wrap;gap:.3rem}
  .btn-ghost{border-radius:.8rem;border:1px solid transparent;background:transparent;padding:.25rem .6rem;font-size:.75rem;display:inline-flex;align-items:center;gap:.2rem;color:var(--muted)}
  .btn-ghost-active{border-color:rgba(236,72,153,.5);background:rgba(236,72,153,.08);color:var(--text)}

  .reader-scroll{max-height:none}
  .readable{column-gap:2.1rem}
  .reader-single .readable{column-count:1}
  .reader-dual .readable{column-count:2}
  .section h2,.section h3{font-weight:800;line-height:1.25;margin:0 0 .75rem}
  .section h2{font-size:1.25rem}
  .section h3{font-size:1.05rem}
  .empty-reader{padding:1rem .9rem;font-size:.8rem;color:var(--muted);text-align:center}

  /* texto geral justificado, mas com quebras normais */
  .prose{
    max-width:none;
    text-align:justify;
    word-break:normal;
    overflow-wrap:break-word;
  }
  .prose p{
    margin-bottom:.9rem;
    text-align:justify;
  }

  /* imagens dentro do artigo */
  .prose img{
    display:block;
    margin:1.5rem auto;
    border-radius:.75rem;
    max-width:100%;
    height:auto;
  }

  /* seção de Referências: mais espaço e quebra "em qualquer lugar" para URLs longas */
  .section-references .prose{
    overflow-wrap:anywhere;
  }
  .section-references .prose p{
    margin-bottom:1.25rem;
  }

  @media(max-width:767.98px){
    .reader-header-top{flex-direction:column-reverse}
    .reader-main-card{border-radius:1.1rem}
  }
</style>
@endpush

@section('content')
<main class="article-shell">
  <div x-data="{ columns: 1, size: 17 }" class="reader-shell">

    {{-- Cabeçalho do artigo --}}
    <header class="reader-header-card">
      <div class="reader-header-top">
        <div class="reader-title-block">
          <div class="reader-label">Artigo · Revista Trivento</div>
          <h1 class="reader-title" title="{{ $submission->title }}">
            {{ $submission->title }}
          </h1>

          <div class="reader-meta">
            @if($autorNome)
              <span><span>Autor:</span><span>{{ $autorNome }}</span></span>
            @endif
            @if($reviewerName)
              <span><span>Revisor:</span><span>{{ $reviewerName }}</span></span>
            @endif
            @if($categoriaNome)
              <span><span>Categoria:</span><span>{{ $categoriaNome }}</span></span>
            @endif
            @if($editionTitle)
              <span><span>Edição:</span><span>{{ $editionTitle }}</span></span>
            @endif
          </div>
        </div>

        @if($statusRaw === 'publicado')
          <div class="reader-toolbar">
            <span class="status-pill">Artigo publicado</span>
          </div>
        @endif
      </div>

      <div class="reader-header-bottom">
        @if($publishedDate)
          <span><span>Publicado em</span><span>{{ $publishedDate->format('d/m/Y') }}</span></span>
        @endif
      </div>
    </header>

    {{-- Corpo da leitura --}}
    <section class="reader-main-card" :class="columns === 2 ? 'reader-dual' : 'reader-single'">
      <div class="sub-toolbar">
        <div class="sub-toolbar-left">
          <span>Leitura</span>
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
              @php
                $sectionTitle = trim(($sec->label ? $sec->label.' ' : '').($sec->title ?? ''));
                $isReferencesSection = \Illuminate\Support\Str::contains(
                    \Illuminate\Support\Str::lower($sectionTitle),
                    ['referência','referencias','references']
                );
                $rawContent = $sec->content_html
                    ?? $sec->content
                    ?? $sec->body
                    ?? '';
                $looksLikeHtml = !empty($sec->content_html) ||
                    \Illuminate\Support\Str::contains($rawContent, ['<p','<br','<ul','<ol','<h']);
              @endphp

              <article class="section break-inside-avoid mb-8 {{ $isReferencesSection ? 'section-references' : '' }}">
                @if($sectionTitle)
                  <h2>{{ $sectionTitle }}</h2>
                @endif

                @if(!empty($rawContent))
                  <div class="prose prose-sm dark:prose-invert">
                    @if($isReferencesSection && !$looksLikeHtml)
                      @foreach(preg_split("/\r\n|\n|\r/", trim($rawContent)) as $refLine)
                        @if(strlen(trim($refLine)))
                          <p>{{ $refLine }}</p>
                        @endif
                      @endforeach
                    @else
                      @if($looksLikeHtml)
                        {!! $rawContent !!}
                      @else
                        {!! nl2br(e($rawContent)) !!}
                      @endif
                    @endif
                  </div>
                @endif

                @if($sec->children && $sec->children->count())
                  @foreach($sec->children as $ch)
                    @php
                      $chTitle = trim(($ch->label ? $ch->label.' ' : '').($ch->title ?? ''));
                      $chContent = $ch->content_html
                          ?? $ch->content
                          ?? $ch->body
                          ?? '';
                      $chLooksLikeHtml = !empty($ch->content_html) ||
                          \Illuminate\Support\Str::contains($chContent, ['<p','<br','<ul','<ol','<h']);
                    @endphp
                    <section class="mt-6 break-inside-avoid">
                      @if($chTitle)
                        <h3>{{ $chTitle }}</h3>
                      @endif
                      @if(!empty($chContent))
                        <div class="prose prose-sm dark:prose-invert">
                          @if($chLooksLikeHtml)
                            {!! $chContent !!}
                          @else
                            {!! nl2br(e($chContent)) !!}
                          @endif
                        </div>
                      @endif
                    </section>
                  @endforeach
                @endif
              </article>
            @endforeach
          </div>
        @elseif($bodyHtmlFallback)
          @php
            $rawContent = $bodyHtmlFallback;
            $looksLikeHtml = \Illuminate\Support\Str::contains($rawContent, ['<p','<br','<ul','<ol','<h']);
          @endphp
          <div class="readable p-4 md:p-6 reader-single" :style="`font-size:${size}px;line-height:1.7`">
            <article class="section break-inside-avoid mb-6">
              <div class="prose prose-sm dark:prose-invert">
                @if($looksLikeHtml)
                  {!! $rawContent !!}
                @else
                  {!! nl2br(e($rawContent)) !!}
                @endif
              </div>
            </article>
          </div>
        @else
          <div class="empty-reader">
            O texto completo desta publicação estará disponível em breve.
          </div>
        @endif
      </div>
    </section>
  </div>
</main>
@endsection
