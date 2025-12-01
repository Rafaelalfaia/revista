@extends('site.layout')

@section('title','Áreas de conhecimento · Revista Trivento')

@push('head')
<style>
  .page-shell{
    max-width:72rem;
    margin:0 auto;
    padding:2.5rem 1.25rem 3.5rem;
  }
  @media(min-width:768px){
    .page-shell{padding:3rem 0 4rem}
  }

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

  .hero-header{
    display:flex;
    gap:1.1rem;
    align-items:flex-start;
  }
  .hero-icon{
    width:2.8rem;
    height:2.8rem;
    border-radius:999px;
    display:flex;
    align-items:center;
    justify-content:center;
    background:var(--panel-2);
    border:1px solid var(--line);
    flex-shrink:0;
  }
  .hero-icon svg{
    width:1.6rem;
    height:1.6rem;
    color:#fb7185;
  }

  .hero-title{
    font-size:1.5rem;
    font-weight:800;
    margin-bottom:.2rem;
  }
  @media(min-width:768px){
    .hero-title{font-size:1.7rem}
  }

  .hero-sub{
    font-size:.92rem;
    color:var(--muted);
    max-width:40rem;
  }

  .hero-kpis{
    display:flex;
    flex-wrap:wrap;
    gap:1rem;
    margin-top:.5rem;
    font-size:.8rem;
  }
  .hero-kpis-item{
    padding:.5rem .75rem;
    border-radius:999px;
    border:1px solid var(--line);
    background:var(--panel-2);
    display:flex;
    align-items:center;
    gap:.4rem;
  }
  .hero-kpis-item span:first-child{
    font-weight:700;
    color:#fb7185;
  }

  .grid-categories{
    display:grid;
    grid-template-columns:repeat(3,minmax(0,1fr));
    gap:1rem;
  }
  @media(max-width:1023.98px){
    .grid-categories{grid-template-columns:repeat(2,minmax(0,1fr))}
  }
  @media(max-width:639.98px){
    .grid-categories{grid-template-columns:1fr}
  }

  .cat-card{
    border-radius:1.4rem;
    border:1px solid var(--line);
    background:var(--panel);
    padding:1rem;
    display:flex;
    flex-direction:column;
    gap:.5rem;
    text-decoration:none;
    transition:border-color .15s ease,box-shadow .15s ease,transform .1s ease;
  }
  .cat-card:hover{
    border-color:#fb7185;
    box-shadow:0 16px 32px rgba(251,113,133,.25);
    transform:translateY(-1px);
  }

  .cat-header{
    display:flex;
    align-items:center;
    gap:.6rem;
  }
  .cat-avatar{
    width:2.2rem;
    height:2.2rem;
    border-radius:.9rem;
    display:flex;
    align-items:center;
    justify-content:center;
    background:linear-gradient(135deg,#fb7185,#ec4899,#6366f1);
    font-size:.95rem;
    font-weight:700;
    color:#f9fafb;
    overflow:hidden;
    flex-shrink:0;
  }
  .cat-avatar img{
    width:100%;
    height:100%;
    object-fit:cover;
  }

  .cat-name{
    font-size:.95rem;
    font-weight:700;
    line-height:1.3;
  }
  .cat-tagline{
    font-size:.72rem;
    color:var(--muted);
  }

  .cat-body{
    font-size:.8rem;
    color:var(--muted);
    line-height:1.6;
    margin-top:.2rem;
  }

  .cat-footer{
    margin-top:.5rem;
    display:flex;
    align-items:center;
    justify-content:space-between;
    font-size:.76rem;
  }
  .cat-pill{
    padding:.18rem .55rem;
    border-radius:999px;
    border:1px solid var(--line);
    font-size:.7rem;
    color:var(--muted);
    background:var(--panel-2);
  }
  .cat-link{
    font-weight:600;
    color:#fb7185;
    display:inline-flex;
    align-items:center;
    gap:.15rem;
  }
</style>
@endpush

@section('content')
<main class="page-shell">
  <section class="hero-card">
    <div class="hero-header">
      <div class="hero-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="7" cy="17" r="3" />
          <circle cx="17" cy="7" r="3" />
          <path d="M10 16l8-8" />
          <path d="M3 7h8" />
          <path d="M13 17h8" />
        </svg>
      </div>
      <div>
        <h1 class="hero-title">Áreas de conhecimento</h1>
        <p class="hero-sub">
          Navegue pelas áreas em que a Revista Trivento recebe publicações. Cada categoria agrupa artigos,
          relatos e demais produções científicas relacionadas ao tema.
        </p>
      </div>
    </div>

    <div class="hero-kpis">
      <div class="hero-kpis-item">
        <span>{{ $categories->count() }}</span>
        <span>áreas cadastradas</span>
      </div>
      <div class="hero-kpis-item">
        <span>Trivento Educação</span>
        <span>organização por eixos temáticos</span>
      </div>
    </div>
  </section>

  @if($categories->isEmpty())
    <p class="text-sm text-[var(--muted)]">
      Ainda não há categorias disponíveis. Assim que forem criadas, aparecerão aqui.
    </p>
  @else
    <section class="grid-categories">
      @foreach($categories as $category)
        @php
          $count   = $category->published_submissions_count ?? 0;
          $desc    = $category->description ?? null;
          $initial = mb_substr($category->name,0,1);
        @endphp
        <a href="{{ route('site.categories.show', $category) }}" class="cat-card">
          <div class="cat-header">
            <div class="cat-avatar">
              @if(!empty($category->icon_url))
                <img src="{{ $category->icon_url }}" alt="{{ $category->name }}">
              @else
                {{ $initial }}
              @endif
            </div>
            <div>
              <div class="cat-name">{{ $category->name }}</div>
              <div class="cat-tagline">
                @if($count === 0)
                  Sem publicações ainda
                @elseif($count === 1)
                  1 publicação
                @else
                  {{ $count }} publicações
                @endif
              </div>
            </div>
          </div>

          @if($desc)
            <div class="cat-body">
              {{ \Illuminate\Support\Str::limit($desc, 160) }}
            </div>
          @endif

          <div class="cat-footer">
            <span class="cat-pill">Ver produções da área</span>
            <span class="cat-link">
              Explorar
              <span>→</span>
            </span>
          </div>
        </a>
      @endforeach
    </section>
  @endif
</main>
@endsection
