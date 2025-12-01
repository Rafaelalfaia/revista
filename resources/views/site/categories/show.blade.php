{{-- D:\revista\resources\views\site\categories\show.blade.php --}}
@extends('site.layout')

@section('title', ($category->name ?? 'Área').' · Revista Trivento')

@php
    use Illuminate\Support\Facades\Route;
    use Illuminate\Support\Collection;

    $all = $submissions ?? collect();

    if (is_array($all)) {
        $all = collect($all);
    }
    if (! $all instanceof Collection) {
        $all = collect($all ?? []);
    }

    $featuredLocal = $all->take(2);
    $othersLocal   = $all->slice($featuredLocal->count());

    $total = $all->count();
    $desc  = $category->description ?? null;

    $query = isset($query) ? $query : request('q');
    $query = is_string($query) ? trim($query) : '';
    $hasFilter = $query !== '';

    $submissionShowUrl = function ($submission) {
        if (! $submission) return '#';
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
  .hero-grid{display:grid;gap:1.4rem;grid-template-columns:minmax(0,1.9fr) minmax(0,.9fr)}
  @media(max-width:1023.98px){.hero-grid{grid-template-columns:1fr}}

  .hero-badge-row{display:flex;flex-wrap:wrap;gap:.4rem;margin-bottom:.6rem}
  .hero-badge{
    font-size:.7rem;text-transform:uppercase;letter-spacing:.12em;
    padding:.25rem .7rem;border-radius:999px;
    border:1px solid rgba(248,250,252,.16);
    background:var(--panel-2);color:var(--text);
  }

  .hero-title{font-size:1.7rem;font-weight:800;margin-bottom:.25rem}
  @media(min-width:768px){.hero-title{font-size:1.9rem}}

  .hero-meta{font-size:.8rem;color:var(--muted);display:flex;flex-wrap:wrap;gap:.4rem}
  .hero-summary{margin-top:1rem;font-size:.9rem;color:var(--text);line-height:1.7;text-align:justify}

  .hero-avatar-box{display:flex;align-items:center;justify-content:flex-end}
  .hero-avatar{
    width:3.4rem;height:3.4rem;border-radius:1.3rem;
    background:linear-gradient(135deg,#fb7185,#ec4899,#6366f1);
    display:flex;align-items:center;justify-content:center;
    font-size:1.4rem;font-weight:800;color:#f9fafb;
    overflow:hidden;
  }
  .hero-avatar img{width:100%;height:100%;object-fit:cover}

  .section-title-row{display:flex;justify-content:space-between;align-items:center;gap:.75rem;margin-bottom:.65rem}
  .section-eyebrow{font-size:.7rem;text-transform:uppercase;letter-spacing:.12em;color:var(--muted)}
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

  .search-row{
    display:flex;
    flex-direction:column;
    gap:.6rem;
    margin-bottom:1.1rem;
  }
  @media(min-width:640px){
    .search-row{
      flex-direction:row;
      align-items:center;
      justify-content:space-between;
    }
  }
  .search-box{
    display:flex;
    align-items:center;
    gap:.4rem;
    width:100%;
    max-width:26rem;
  }
  .search-input{
    flex:1 1 auto;
    border-radius:999px;
    border:1px solid var(--line);
    background:var(--panel-2);
    padding:.45rem .85rem;
    font-size:.8rem;
    color:var(--text);
  }
  .search-input::placeholder{
    color:var(--muted);
  }
  .search-btn{
    border-radius:999px;
    border:none;
    padding:.45rem .9rem;
    font-size:.78rem;
    font-weight:600;
    background:var(--brand);
    color:#f9fafb;
    cursor:pointer;
  }
  .search-meta{
    font-size:.75rem;
    color:var(--muted);
  }
  .search-clear{
    font-size:.75rem;
    color:var(--brand);
    text-decoration:none;
    margin-left:.25rem;
  }
</style>
@endpush

@section('content')
<main class="page-shell">
  <section class="hero-wrap">
    <div class="hero-grid">
      <div>
        <div class="hero-badge-row">
          <span class="hero-badge">Área de conhecimento</span>
          <span class="hero-badge">
            @if($total === 0)
              Sem publicações ainda
            @elseif($total === 1)
              1 publicação
            @else
              {{ $total }} publicações
            @endif
          </span>
        </div>

        <h1 class="hero-title">{{ $category->name }}</h1>

        <div class="hero-meta">
          <span>Revista Trivento · Trivento Educação</span>
        </div>

        @if($desc)
          <div class="hero-summary">
            {{ $desc }}
          </div>
        @endif
      </div>

      <div class="hero-avatar-box">
        <div class="hero-avatar">
          @if(!empty($category->icon_url))
            <img src="{{ $category->icon_url }}" alt="{{ $category->name }}">
          @else
            {{ mb_substr($category->name,0,1) }}
          @endif
        </div>
      </div>
    </div>
  </section>

  <section class="mb-8">
    <div class="section-title-row">
      <div>
        <div class="section-eyebrow">
          {{ $featuredLocal->isNotEmpty() ? 'Destaques da área' : 'Produções da área' }}
        </div>
        <h2 class="section-title">Publicações em {{ $category->name }}</h2>
      </div>
      <a href="{{ route('site.categories.index') }}" class="text-xs sm:text-sm font-semibold text-[var(--brand)]">
        Voltar para categorias
      </a>
    </div>

    <div class="search-row">
      <form method="GET" action="{{ route('site.categories.show', $category) }}" class="search-box">
        <input
          type="text"
          name="q"
          value="{{ $query }}"
          class="search-input"
          placeholder="Buscar por título ou palavra-chave">
        <button type="submit" class="search-btn">Buscar</button>
      </form>

      <div class="search-meta">
        @if($hasFilter)
          Exibindo {{ $total }} resultado(s) para
          <strong>"{{ $query }}"</strong>.
          <a href="{{ route('site.categories.show', $category) }}" class="search-clear">Limpar filtro</a>
        @else
          {{ $total }} publicação(ões) nesta área.
        @endif
      </div>
    </div>

    @if($all->isEmpty())
      <p class="empty-msg">
        @if($hasFilter)
          Nenhuma publicação encontrada nesta área para "{{ $query }}".
        @else
          Ainda não há publicações disponíveis nesta área de conhecimento.
        @endif
      </p>
    @else
      @if($featuredLocal->isNotEmpty())
        <div class="mb-6">
          <div class="flex items-center justify-between mb-2">
            <span class="pill-small">{{ $featuredLocal->count() }} em destaque</span>
          </div>

          <div class="pub-grid">
            @foreach($featuredLocal as $submission)
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
            {{ $all->count() }} publicações
          </span>
        </div>

        <div class="pub-grid">
          @foreach(($featuredLocal->isNotEmpty() ? $othersLocal : $all) as $submission)
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
