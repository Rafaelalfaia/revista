@extends('console.layout')
@section('title','Comentários')
@section('page.title','Comentários da Submissão')

@push('head')
<style>
  .read-shell{display:grid;gap:1rem}
  @media(min-width:1024px){.read-shell{grid-template-columns:1fr 390px}}
  .read-pane{border:1px solid var(--line);border-radius:1rem;background:var(--panel)}
  .read-body{padding:1rem 1rem 1.25rem}
  .read-section{padding:1rem;border-top:1px solid var(--line)}
  .read-section:first-child{border-top:0}
  .read-section h3{font-weight:700;margin:0 0 .25rem}
  .read-content{line-height:1.65}
  .asset-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.75rem;margin-top:.5rem}
  .asset-card{border:1px solid var(--line);border-radius:.75rem;overflow:hidden;background:var(--panel)}
  .asset-card figcaption{font-size:.75rem;color:var(--muted);padding:.4rem .5rem}
  .side-pane{position:sticky;top:1rem;align-self:start;border:1px solid var(--line);border-radius:1rem;background:var(--panel);padding:1rem}
</style>
@endpush

@section('content')
  @php
    $secs = $submission->sections()->orderBy('position')->get();
    $assetsBySection = method_exists($submission,'assets')
      ? $submission->assets()->get()->groupBy('section_id')
      : collect();
  @endphp

  <div class="read-shell">
    <div class="read-pane">
      <div class="read-body">
        <h1 class="text-xl font-bold truncate">{{ $submission->title }}</h1>
        <div class="text-sm muted">#{{ $submission->id }} — {{ $submission->status_label ?? $submission->status }}</div>
      </div>

      @foreach($secs as $s)
        <article id="sec-{{ $s->id }}" class="read-section" data-section-id="{{ $s->id }}">
          <h3>{{ $s->title }}</h3>
          <div class="read-content" data-content-for="{{ $s->id }}">{!! $s->content !!}</div>

          @php $secAssets = ($assetsBySection->get($s->id) ?? collect())->values(); @endphp
          @if($secAssets->count())
            <div class="asset-grid">
              @foreach($secAssets as $a)
                @if(($a->type ?? '') === 'figure' && $a->file_path)
                  <figure class="asset-card">
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($a->file_path) }}" alt="{{ $a->caption ?? 'Figura' }}">
                    @if($a->caption)
                      <figcaption>{{ $a->caption }}</figcaption>
                    @endif
                  </figure>
                @endif
              @endforeach
            </div>
          @endif
        </article>
      @endforeach
    </div>

    <aside class="side-pane">
      @include('submissions.partials.comments-panel', [
        'submission' => $submission,
        'review'     => $review ?? null,
      ])
    </aside>
  </div>
@endsection
