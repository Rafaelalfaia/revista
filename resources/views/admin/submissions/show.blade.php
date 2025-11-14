@extends('console.layout')
@section('title','Leitura — Submissão')
@section('page.title','Leitura da Submissão')

@push('head')
<style>
  .toolbar .btn{border:1px solid var(--line);background:var(--panel);color:var(--text);border-radius:.75rem;padding:.5rem .9rem;font-weight:600}
  .reader{border:1px solid var(--line);background:var(--panel);border-radius:1rem}
  .readable{column-gap:2.2rem}
  .reader.single .readable{column-count:1}
  .reader.dual .readable{column-count:2}
  .section h2,.section h3{font-weight:800;line-height:1.25;margin:0 0 .5rem}
  .section h2{font-size:1.35rem}
  .section h3{font-size:1.15rem}
</style>
@endpush

@section('content')
  <div x-data="{ reader:{columns:2,size:17} }" class="grid gap-3">
    <div class="flex items-start justify-between gap-3">
      <div class="min-w-0">
        <div class="text-xs muted">Submissão</div>
        <div class="font-semibold truncate" title="{{ $submission->title }}">{{ $submission->title }}</div>
        <div class="mt-1 text-xs muted truncate">
          Autor: {{ $submission->user->name ?? '—' }}
          @if(!empty($reviewers) && count($reviewers))
            · Revisor(es): {{ collect($reviewers)->join(', ') }}
          @endif
        </div>
      </div>
      <div class="toolbar flex items-center gap-2 shrink-0">
        <button type="button" class="btn" @click="reader.columns=1">1 coluna</button>
        <button type="button" class="btn" @click="reader.columns=2">2 colunas</button>
        <button type="button" class="btn" @click="reader.size=Math.max(15,reader.size-1)">A−</button>
        <button type="button" class="btn" @click="reader.size=Math.min(22,reader.size+1)">A+</button>
      </div>
    </div>

    <div class="reader" :class="reader.columns===2 ? 'dual' : 'single'">
      <div class="readable p-5 md:p-8" :style="`font-size:${reader.size}px;line-height:1.7`">
        @foreach ($roots as $sec)
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
    </div>
  </div>

  @push('scripts')
  <script>
    try {
      localStorage.setItem('trv.menu','false');
      document.body.classList.remove('overflow-hidden');
      const aside = document.getElementById('console-sidebar');
      if (aside) aside.removeAttribute('inert');
    } catch(e) {}
  </script>
  @endpush
@endsection
