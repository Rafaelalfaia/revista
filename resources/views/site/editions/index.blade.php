@extends('site.layout')

@section('title','Edições · Revista Trivento')

@push('head')
<style>
  .page-shell{max-width:72rem;margin:0 auto;padding:2.5rem 1.25rem 3.5rem}
  @media(min-width:768px){.page-shell{padding:3rem 0 4rem}}

  .hero-card{
    border-radius:1.5rem;
    border:1px solid var(--line);
    background:
      radial-gradient(circle at top left,rgba(244,114,182,.22),transparent 55%),
      radial-gradient(circle at bottom right,rgba(236,72,153,.15),transparent 55%),
      var(--panel);
    padding:1.8rem 1.7rem;
    margin-bottom:2.2rem;
    display:flex;
    flex-direction:column;
    gap:1rem;
  }

  .hero-header{display:flex;gap:1.1rem;align-items:flex-start}
  .hero-icon{
    width:2.8rem;height:2.8rem;border-radius:999px;
    display:flex;align-items:center;justify-content:center;
    background:rgba(15,23,42,.9);
    border:1px solid rgba(248,250,252,.06);
    flex-shrink:0;
  }
  .hero-icon svg{width:1.6rem;height:1.6rem;color:#fb7185}

  .hero-title{font-size:1.5rem;font-weight:800;margin-bottom:.2rem}
  @media(min-width:768px){.hero-title{font-size:1.7rem}}
  .hero-sub{font-size:.92rem;color:var(--muted);max-width:40rem}
  .hero-kpis{display:flex;flex-wrap:wrap;gap:1rem;margin-top:.5rem;font-size:.8rem}
  .hero-kpis-item{
    padding:.5rem .75rem;border-radius:999px;
    border:1px solid rgba(148,163,184,.4);
    background:rgba(15,23,42,.6);
    display:flex;align-items:center;gap:.4rem;
  }
  .hero-kpis-item span:first-child{font-weight:700;color:#fb7185}

  .editions-grid{display:flex;flex-direction:column;gap:1rem}
  .edition-card{
    border-radius:1.4rem;
    border:1px solid var(--line);
    background:var(--panel);
    padding:.9rem .9rem;
    display:flex;
    gap:.9rem;
    text-decoration:none;
    transition:border-color .15s ease,box-shadow .15s ease,transform .12s ease;
  }
  .edition-card:hover{
    border-color:#fb7185;
    box-shadow:0 14px 30px rgba(251,113,133,.25);
    transform:translateY(-1px);
  }

  .edition-cover{
    width:3.5rem;height:4.9rem;border-radius:1rem;overflow:hidden;
    background:linear-gradient(135deg,#fb7185,#ec4899,#6366f1);
    flex-shrink:0;position:relative;
  }
  .edition-cover img{width:100%;height:100%;object-fit:cover}
  .edition-cover-badge{
    position:absolute;left:.4rem;right:.4rem;bottom:.35rem;
    padding:.12rem .4rem;border-radius:.55rem;
    background:rgba(15,23,42,.85);color:#f9fafb;
    font-size:.64rem;text-align:center;
  }

  .edition-main{flex:1;min-width:0;display:flex;flex-direction:column;gap:.25rem}
  .edition-topline{
    font-size:.65rem;text-transform:uppercase;
    letter-spacing:.12em;color:var(--muted);
  }
  .edition-title{
    font-size:.95rem;font-weight:700;line-height:1.35;
    display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;
  }
  .edition-meta{font-size:.78rem;color:var(--muted);display:flex;flex-wrap:wrap;gap:.45rem}

  .edition-pill{
    font-size:.7rem;padding:.18rem .6rem;border-radius:999px;
    border:1px solid rgba(236,72,153,.5);
    color:#fb7185;background:rgba(236,72,153,.1);
    align-self:flex-start;
  }

  @media(max-width:639.98px){
    .edition-card{padding:.9rem .85rem}
  }
</style>
@endpush

@section('content')
<main class="page-shell">
  <section class="hero-card">
    <div class="hero-header">
      <div class="hero-icon">
        {{-- Ícone simples de livros empilhados --}}
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
          <path d="M4 4h10a2 2 0 0 1 2 2v12a2 2 0 0 0-2-2H4z" />
          <path d="M4 8h10" />
          <path d="M8 4v16" />
          <path d="M14 6h4a2 2 0 0 1 2 2v12a2 2 0 0 0-2-2h-4" />
          <path d="M14 10h6" />
        </svg>
      </div>
      <div>
        <h1 class="hero-title">Edições da Revista Trivento</h1>
        <p class="hero-sub">
          Explore as edições já publicadas da revista, com acesso direto às publicações de cada número.
          A edição mais recente aparece em destaque.
        </p>
      </div>
    </div>

    <div class="hero-kpis">
      <div class="hero-kpis-item">
        <span>{{ $editions->count() }}</span>
        <span>edições disponíveis</span>
      </div>
      @if($currentEdition)
        @php
          $year = $currentEdition->release_date?->format('Y') ?? $currentEdition->published_at?->format('Y');
        @endphp
        <div class="hero-kpis-item">
          <span>Atual</span>
          <span>{{ $year ? 'Edição ' . $year : $currentEdition->title }}</span>
        </div>
      @endif
    </div>
  </section>

  @if($editions->isEmpty())
    <p class="text-sm text-[var(--muted)]">
      Ainda não há edições publicadas. Assim que as primeiras forem lançadas, elas aparecerão aqui.
    </p>
  @else
    <section class="editions-grid">
      @foreach($editions as $edition)
        @php
          $isCurrent  = $currentEdition && $currentEdition->id === $edition->id;
          $year       = $edition->release_date?->format('Y') ?? $edition->published_at?->format('Y');
          $date       = $edition->published_at ?? $edition->release_date ?? null;
          $desc       = $edition->summary ?? $edition->description ?? null;
          $badgeLabel = $isCurrent ? 'Edição atual' : 'Arquivada';
          $topline    = $year ? 'Edição ' . $year : 'Edição';
        @endphp
        <a href="{{ route('site.editions.show', $edition) }}" class="edition-card">
          <div class="edition-cover">
            @if(!empty($edition->cover_url))
              <img src="{{ $edition->cover_url }}" alt="Capa da edição {{ $edition->title }}">
            @endif
            <div class="edition-cover-badge">{{ $badgeLabel }}</div>
          </div>

          <div class="edition-main">
            <div class="edition-topline">{{ $topline }}</div>
            <div class="edition-title">{{ $edition->title }}</div>

            <div class="edition-meta">
              @if($date)
                <span>Publicado em {{ $date->format('d/m/Y') }}</span>
              @endif
              @if(!empty($edition->volume))
                <span>Volume {{ $edition->volume }}</span>
              @endif
              @if(!empty($edition->number))
                <span>Número {{ $edition->number }}</span>
              @endif
            </div>

            @if($desc)
              <div class="text-[.78rem] text-[var(--muted)] line-clamp-2 mt-1">
                {{ $desc }}
              </div>
            @endif
          </div>

          @if($isCurrent)
            <span class="edition-pill">Atual</span>
          @endif
        </a>
      @endforeach
    </section>
  @endif
</main>
@endsection
