@extends('site.layout')

@section('title', ($edition->title ?? 'Edição').' · Revista Trivento')

@php
    use Illuminate\Support\Facades\Route;

    $year = $edition->release_date?->format('Y')
        ?? $edition->published_at?->format('Y');

    $publishedDate = $edition->published_at
        ?? $edition->release_date
        ?? null;

    $subtitle = $edition->subtitle ?? null;
    $summary  = $edition->summary ?? $edition->description ?? null;

    $submissionShowUrl = function ($submission) {
        if (!$submission) return '#';
        if (Route::has('site.submissions.show')) {
            return route('site.submissions.show', $submission);
        }
        if (Route::has('admin.submissions.show')) {
            return route('admin.submissions.show', $submission);
        }
        return '#';
    };
@endphp

@push('head')
<style>
  .page-shell{max-width:72rem;margin:0 auto;padding:2.5rem 1.25rem 3.5rem}
  @media(min-width:768px){.page-shell{padding:3rem 0 4rem}}

  .hero-wrap{
    border-radius:1.6rem;
    border:1px solid var(--line);
    background:
      radial-gradient(circle at top left,rgba(244,114,182,.22),transparent 55%),
      radial-gradient(circle at bottom right,rgba(236,72,153,.15),transparent 55%),
      var(--panel);
    padding:1.7rem 1.6rem 1.8rem;
    margin-bottom:2.2rem;
  }
  .hero-grid{display:grid;gap:1.4rem;grid-template-columns:minmax(0,1.6fr) minmax(0,1.1fr)}
  @media(max-width:1023.98px){.hero-grid{grid-template-columns:1fr}}

  .hero-badge-row{display:flex;flex-wrap:wrap;gap:.4rem;margin-bottom:.6rem}
  .hero-badge{
    font-size:.7rem;text-transform:uppercase;letter-spacing:.12em;
    padding:.25rem .7rem;border-radius:999px;
    border:1px solid rgba(248,250,252,.16);
    background:rgba(15,23,42,.7);color:#f9fafb;
  }
  .hero-title{font-size:1.7rem;font-weight:800;margin-bottom:.25rem}
  @media(min-width:768px){.hero-title{font-size:1.9rem}}
  .hero-subtitle{font-size:.9rem;font-weight:500;color:var(--muted);margin-bottom:.35rem}
  .hero-meta{font-size:.8rem;color:var(--muted);display:flex;flex-wrap:wrap;gap:.4rem}
  .hero-summary{margin-top:1rem;font-size:.9rem;color:var(--text);line-height:1.7;text-align:justify}

  .hero-cover{
    position:relative;width:100%;max-width:260px;margin-left:auto;
    border-radius:1.4rem;overflow:hidden;
    box-shadow:0 22px 45px rgba(251,113,133,.35);
    background:linear-gradient(135deg,#fb7185,#ec4899,#6366f1);
    aspect-ratio:3/4;
  }
  .hero-cover img{width:100%;height:100%;object-fit:cover}
  .hero-cover-overlay{
    position:absolute;inset:0;
    background:linear-gradient(to top,rgba(0,0,0,.7),rgba(0,0,0,.25),transparent);
  }
  .hero-cover-footer{
    position:absolute;inset-x:0;bottom:0;padding:.8rem .9rem 1rem;color:#f9fafb;
  }
  .hero-cover-footer p:first-child{
    font-size:.72rem;text-transform:uppercase;
    letter-spacing:.16em;opacity:.85;
  }
  .hero-cover-footer p:last-child{
    font-size:.9rem;font-weight:700;line-height:1.35;
  }

  .section-title-row{
    display:flex;justify-content:space-between;align-items:center;
    gap:.75rem;margin-bottom:.65rem;
  }
  .section-eyebrow{
    font-size:.7rem;text-transform:uppercase;
    letter-spacing:.12em;color:var(--muted);
  }
  .section-title{font-size:1.05rem;font-weight:700}

  .pill-small{
    font-size:.7rem;padding:.15rem .65rem;border-radius:999px;
    border:1px solid rgba(236,72,153,.5);
    background:rgba(236,72,153,.08);color:#fb7185;
  }

  .pub-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.9rem}
  @media(max-width:767.98px){.pub-grid{grid-template-columns:1fr}}

  .pub-card{
    border-radius:1.1rem;border:1px solid var(--line);
    background:var(--panel);padding:.9rem .9rem 1rem;
    display:flex;flex-direction:column;gap:.3rem;
    text-decoration:none;
    transition:border-color .15s ease,box-shadow .15s ease,transform .1s ease;
  }
  .pub-card:hover{
    border-color:#fb7185;
    box-shadow:0 16px 32px rgba(251,113,133,.28);
    transform:translateY(-1px);
  }

  .pub-topline{
    font-size:.7rem;color:var(--muted);
    display:flex;flex-wrap:wrap;gap:.35rem;align-items:center;
  }
  .chip{
    padding:.12rem .5rem;border-radius:999px;
    border:1px solid rgba(148,163,184,.5);
    font-size:.7rem;
  }

  .pub-title{
    font-size:.95rem;font-weight:700;line-height:1.35;
    margin-top:.1rem;
    display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;
  }

  .pub-meta{
    font-size:.78rem;color:var(--muted);
    display:flex;flex-wrap:wrap;gap:.35rem;margin-top:.25rem;
  }

  .pub-footer{
    margin-top:.45rem;font-size:.76rem;font-weight:600;
    color:#fb7185;display:inline-flex;align-items:center;gap:.18rem;
  }

  .empty-msg{font-size:.85rem;color:var(--muted);padding:1rem .2rem 0}
</style>
@endpush

@section('content')
<main class="page-shell">
  <section class="hero-wrap">
    <div class="hero-grid">
      <div>
        <div class="hero-badge-row">
          @if($year)
            <span class="hero-badge">Edição {{ $year }}</span>
          @else
            <span class="hero-badge">Edição</span>
          @endif
          @if($publishedDate)
            <span class="hero-badge">Publicado em {{ $publishedDate->format('d/m/Y') }}</span>
          @endif
          @if(!empty($edition->volume))
            <span class="hero-badge">Volume {{ $edition->volume }}</span>
          @endif
          @if(!empty($edition->number))
            <span class="hero-badge">Número {{ $edition->number }}</span>
          @endif
        </div>

        <h1 class="hero-title">{{ $edition->title }}</h1>

        @if($subtitle)
          <div class="hero-subtitle">{{ $subtitle }}</div>
        @endif

        <div class="hero-meta">
          @if(!empty($edition->issn))
            <span>ISSN {{ $edition->issn }}</span>
          @endif
          @if(!empty($edition->city))
            <span>{{ $edition->city }}</span>
          @endif
        </div>

        @if($summary)
          <div class="hero-summary">
            {{ $summary }}
          </div>
        @endif
      </div>

      <div class="flex items-center justify-end">
        <div class="hero-cover">
          @if(!empty($coverUrl))
            <img src="{{ $coverUrl }}" alt="Capa da edição {{ $edition->title }}">
            <div class="hero-cover-overlay"></div>
          @endif
          <div class="hero-cover-footer">
            <p>Revista Trivento</p>
            <p>{{ $edition->title }}</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="mb-8">
    <div class="section-title-row">
      <div>
        <div class="section-eyebrow">Conteúdo da edição</div>
        <h2 class="section-title">Publicações desta edição</h2>
      </div>
      <a href="{{ route('site.editions.index') }}" class="text-xs sm:text-sm font-semibold text-[var(--brand)]">
        Voltar para edições
      </a>
    </div>

    @if($submissions->isEmpty())
      <p class="empty-msg">
        Ainda não há publicações disponíveis para esta edição.
      </p>
    @else
      @if($featured->isNotEmpty())
        <div class="mb-6">
          <div class="flex items-center justify-between mb-2">
            <div class="section-eyebrow">Destaques</div>
            <span class="pill-small">{{ $featured->count() }} em destaque</span>
          </div>

          <div class="pub-grid">
            @foreach($featured as $submission)
              @php
                $link   = $submissionShowUrl($submission);
                $cat    = $submission->categories->first();
                $author = optional($submission->author)->name ?? optional($submission->user)->name;
                $date   = $submission->published_at ?? $submission->accepted_at ?? $submission->submitted_at ?? $submission->created_at;
              @endphp
              <a href="{{ $link }}" class="pub-card">
                <div class="pub-topline">
                  @if($cat)
                    <span class="chip">{{ $cat->name }}</span>
                  @endif
                  @if($submission->type ?? false)
                    <span>{{ $submission->type }}</span>
                  @endif
                </div>
                <div class="pub-title">
                  {{ $submission->title }}
                </div>
                <div class="pub-meta">
                  @if($author)
                    <span>Autor(a): {{ $author }}</span>
                  @endif
                  @if($date)
                    <span>{{ $date->format('d/m/Y') }}</span>
                  @endif
                </div>
                <div class="pub-footer">
                  <span>Ler publicação</span>
                  <span>→</span>
                </div>
              </a>
            @endforeach
          </div>
        </div>
      @endif

      <div>
        <div class="flex items-center justify-between mb-2">
          <div class="section-eyebrow">Lista completa</div>
          <span class="text-[.75rem] text-[var(--muted)]">
            {{ $submissions->count() }} publicações
          </span>
        </div>

        <div class="pub-grid">
          @foreach($others as $submission)
            @php
              $link   = $submissionShowUrl($submission);
              $cat    = $submission->categories->first();
              $author = optional($submission->author)->name ?? optional($submission->user)->name;
              $date   = $submission->published_at ?? $submission->accepted_at ?? $submission->submitted_at ?? $submission->created_at;
            @endphp
            <a href="{{ $link }}" class="pub-card">
              <div class="pub-topline">
                @if($cat)
                  <span class="chip">{{ $cat->name }}</span>
                @endif
                @if($submission->type ?? false)
                  <span>{{ $submission->type }}</span>
                @endif
              </div>
              <div class="pub-title">
                {{ $submission->title }}
              </div>
              <div class="pub-meta">
                @if($author)
                  <span>Autor(a): {{ $author }}</span>
                @endif
                @if($date)
                  <span>{{ $date->format('d/m/Y') }}</span>
                @endif
              </div>
              <div class="pub-footer">
                <span>Ver detalhes</span>
                <span>→</span>
              </div>
            </a>
          @endforeach
        </div>
      </div>
    @endif
  </section>
</main>
@endsection
