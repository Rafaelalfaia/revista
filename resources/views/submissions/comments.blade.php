@extends($layout ?? 'console.layout')
@section('title','Comentários')
@section('page.title','Comentários da Submissão')

@push('head')
<style>
  .cmt-shell{display:flex;flex-direction:column;gap:1rem}
  @media(min-width:1024px){
    .cmt-shell{display:grid;grid-template-columns:minmax(0,2.1fr)minmax(0,1fr)}
  }

  .cmt-main-card{border-radius:1.3rem;border:1px solid var(--line);background:radial-gradient(circle at top left,rgba(251,113,133,.14),transparent 55%),radial-gradient(circle at top right,rgba(59,130,246,.18),transparent 55%),var(--panel);overflow:hidden;display:flex;flex-direction:column}
  .cmt-main-head{padding:.9rem 1rem;border-bottom:1px solid rgba(148,163,184,.4);display:flex;flex-direction:column;gap:.35rem}
  .cmt-title{font-size:1.05rem;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
  .cmt-meta{font-size:.78rem;color:var(--muted)}
  .cmt-main-body{padding:.85rem .95rem 1.1rem;display:flex;flex-direction:column;gap:.7rem}

  .cmt-section{border-radius:1.1rem;border:1px solid var(--line);padding:.7rem .8rem;background:var(--panel);scroll-margin-top:4.5rem;display:flex;flex-direction:column;gap:.4rem}
  .cmt-section-title{font-size:.9rem;font-weight:600}
  .cmt-section-content{font-size:.85rem;line-height:1.6;word-wrap:break-word}
  .cmt-section-content p{margin-bottom:.5rem}
  .cmt-section-content p:last-child{margin-bottom:0}

  .asset-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.6rem;margin-top:.35rem}
  @media(min-width:1024px){
    .asset-grid{gap:.75rem}
  }
  .asset-card{border:1px solid var(--line);border-radius:.75rem;overflow:hidden;background:var(--panel)}
  .asset-card img{display:block;width:100%;height:auto}
  .asset-card figcaption{font-size:.72rem;color:var(--muted);padding:.35rem .45rem}

  .cmt-side{border-radius:1.2rem;border:1px solid var(--line);background:var(--panel);padding:.85rem .9rem;display:flex;flex-direction:column;gap:.85rem}
  @media(min-width:1024px){
    .cmt-side{position:sticky;top:1rem;align-self:flex-start}
  }

  .cmt-side-block-title{font-size:.85rem;font-weight:600;margin-bottom:.35rem}
  .cmt-review-card{border-radius:.85rem;border:1px solid var(--line);padding:.55rem .6rem;background:rgba(15,23,42,.02);font-size:.8rem;display:flex;flex-direction:column;gap:.25rem}
  .cmt-review-meta{font-size:.7rem;color:var(--muted);display:flex;flex-wrap:wrap;gap:.25rem;justify-content:space-between}
  .cmt-review-body{font-size:.8rem;line-height:1.5}
  .cmt-review-empty{font-size:.78rem;color:var(--muted)}
</style>
@endpush

@section('content')
@php
  $secs = $submission->sections()->orderBy('position')->get();
  $assetsBySection = method_exists($submission,'assets')
    ? $submission->assets()->get()->groupBy('section_id')
    : collect();

  $isAuthor  = auth()->id() === $submission->user_id;
  $canVerify = auth()->user()?->hasAnyRole(['Revisor','Admin','Coordenador']) ?? false;
@endphp

<div class="cmt-shell">
  <div class="cmt-main-card">
    <div class="cmt-main-head">
      <div class="cmt-title">{{ $submission->title }}</div>
      <div class="cmt-meta">
        #{{ $submission->id }}
        <span>•</span>
        <span>{{ $submission->status_label ?? $submission->status }}</span>
      </div>
    </div>

    <div class="cmt-main-body">
      @foreach($secs as $s)
        <article id="sec-{{ $s->id }}" class="cmt-section" data-section-id="{{ $s->id }}">
          <h3 class="cmt-section-title">{{ $s->title }}</h3>
          <div class="cmt-section-content" data-content-for="{{ $s->id }}">{!! $s->content !!}</div>

          @php $secAssets = ($assetsBySection->get($s->id) ?? collect())->values(); @endphp
          @if($secAssets->count())
            <div class="asset-grid">
              @foreach($secAssets as $a)
                @if(($a->type ?? '') === 'figure' && $a->file_path)
                  <figure class="asset-card">
                    <img src="{{ \Illuminate\Support\Facades\Storage::disk($a->disk ?: 'public')->url($a->file_path) }}"
                         alt="{{ $a->caption ?? 'Figura' }}">
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
  </div>

  <aside class="cmt-side">
    @if(isset($reviews) && $reviews->count())
      <div>
        <div class="cmt-side-block-title">Parecer(es) do Revisor</div>
        <div class="space-y-2.5">
          @foreach($reviews as $rev)
            <div class="cmt-review-card">
              <div class="cmt-review-meta">
                <span>{{ $rev->reviewer->name ?? 'Revisor' }}</span>
                <span>{{ optional($rev->submitted_opinion_at)->format('d/m/Y H:i') }}</span>
              </div>
              @if($rev->notes)
                <div class="cmt-review-body">{!! nl2br(e($rev->notes)) !!}</div>
              @else
                <div class="cmt-review-empty">Sem observações textuais.</div>
              @endif
            </div>
          @endforeach
        </div>
      </div>
    @endif

    @include('submissions.partials.comments-panel', [
      'submission' => $submission,
      'comments'   => $comments ?? null,
      'reviews'    => $reviews ?? null,
      'isAuthor'   => $isAuthor,
      'canVerify'  => $canVerify,
    ])
  </aside>
</div>
@endsection
