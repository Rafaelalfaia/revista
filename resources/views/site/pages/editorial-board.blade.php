@extends('site.layout')

@section('title','Conselho editorial · Revista Trivento')

@push('head')
<style>
  .page-shell{max-width:72rem;margin:0 auto;padding:2.5rem 1.25rem 3.5rem}
  @media(min-width:768px){
    .page-shell{padding:3rem 0 4rem}
  }

  .header-card{
    border-radius:1.5rem;
    border:1px solid var(--line);
    background:
      radial-gradient(circle at top left,rgba(244,114,182,.22),transparent 55%),
      radial-gradient(circle at bottom right,rgba(236,72,153,.15),transparent 55%),
      var(--panel);
    padding:1.7rem 1.6rem;
    margin-bottom:2.2rem;
    display:flex;
    gap:1.4rem;
    align-items:flex-start;
  }

  .header-icon{
    width:2.8rem;
    height:2.8rem;
    border-radius:999px;
    display:flex;
    align-items:center;
    justify-content:center;
    background:rgba(244,114,182,.15);
    color:#f9a8d4;
    font-size:1.5rem;
    flex-shrink:0;
  }

  .header-title{font-size:1.5rem;font-weight:800;margin-bottom:.3rem}
  @media(min-width:768px){
    .header-title{font-size:1.7rem}
  }
  .header-sub{font-size:.92rem;color:var(--muted);max-width:40rem}
  .header-tags{display:flex;flex-wrap:wrap;gap:.4rem;margin-top:.9rem}
  .header-tag{
    font-size:.7rem;
    text-transform:uppercase;
    letter-spacing:.08em;
    padding:.18rem .6rem;
    border-radius:999px;
    border:1px solid rgba(148,163,184,.5);
    color:var(--muted);
  }

  .board-grid{
    display:grid;
    grid-template-columns:repeat(3,minmax(0,1fr));
    gap:1.3rem;
  }
  @media(max-width:1023.98px){
    .board-grid{grid-template-columns:repeat(2,minmax(0,1fr))}
  }
  @media(max-width:639.98px){
    .board-grid{grid-template-columns:1fr}
  }

  .reviewer-card{
    border-radius:1.4rem;
    border:1px solid var(--line);
    background:var(--panel);
    padding:1.2rem 1.1rem 1.25rem;
    display:flex;
    flex-direction:column;
    gap:.85rem;
  }

  .reviewer-top{display:flex;gap:.75rem;align-items:center}
  .avatar-wrap{
    width:3rem;height:3rem;border-radius:999px;overflow:hidden;
    position:relative;flex-shrink:0;
    background:radial-gradient(circle at top left,rgba(236,72,153,.18),transparent 60%),#020617;
    display:flex;align-items:center;justify-content:center;
    font-weight:700;font-size:1.1rem;color:#fecaca;
  }
  .avatar-wrap img{width:100%;height:100%;object-fit:cover}

  .reviewer-name{font-size:.98rem;font-weight:700}
  .reviewer-role{font-size:.75rem;color:var(--muted)}
  .reviewer-extra{font-size:.78rem;color:var(--muted);margin-top:.1rem}

  .cat-row{display:flex;flex-wrap:wrap;gap:.4rem;margin-top:.1rem}
  .cat-pill{
    font-size:.7rem;
    padding:.18rem .5rem;
    border-radius:999px;
    background:rgba(15,23,42,.9);
    border:1px solid rgba(148,163,184,.35);
    color:var(--muted);
  }

  .reviewer-footer{margin-top:.2rem;font-size:.75rem;color:var(--muted);display:flex;justify-content:space-between;gap:.5rem;flex-wrap:wrap}
  .badge-pill{
    font-size:.7rem;
    padding:.15rem .5rem;
    border-radius:999px;
    background:rgba(236,72,153,.08);
    color:#fb7185;
    border:1px solid rgba(236,72,153,.4);
  }

  .empty-state{
    margin-top:1.5rem;
    font-size:.85rem;
    color:var(--muted);
    text-align:center;
  }
</style>
@endpush

@section('content')
<main class="page-shell">
  <section class="header-card">
    <div class="header-icon">
      👥
    </div>
    <div>
      <h1 class="header-title">Conselho editorial</h1>
      <p class="header-sub">
        Conheça a equipe de revisores que apoia o processo de avaliação científica da Revista Trivento,
        organizada por áreas de atuação e linhas temáticas.
      </p>
      <div class="header-tags">
        <span class="header-tag">Avaliação por pares</span>
        <span class="header-tag">Qualidade científica</span>
        <span class="header-tag">Diversidade de áreas</span>
      </div>
    </div>
  </section>

  @if($reviewers->count())
    <section class="board-grid">
      @foreach($reviewers as $reviewer)
        @php
          $avatar =
            $reviewer->profile_photo_url
            ?? $reviewer->avatar_url
            ?? $reviewer->photo_url
            ?? null;

          $initial = mb_substr($reviewer->name ?? 'R', 0, 1);
          $categories = $reviewer->categories ?? collect();
        @endphp

        <article class="reviewer-card">
          <div class="reviewer-top">
            <div class="avatar-wrap">
              @if($avatar)
                <img src="{{ $avatar }}" alt="Foto de perfil de {{ $reviewer->name }}">
              @else
                <span>{{ mb_strtoupper($initial) }}</span>
              @endif
            </div>
            <div>
              <div class="reviewer-name">{{ $reviewer->name }}</div>
              <div class="reviewer-role">Revisor(a)</div>
              @if($reviewer->institution ?? false)
                <div class="reviewer-extra">{{ $reviewer->institution }}</div>
              @endif
            </div>
          </div>

          @if($categories->count())
            <div class="cat-row">
              @foreach($categories as $cat)
                <span class="cat-pill">{{ $cat->name }}</span>
              @endforeach
            </div>
          @endif

          <div class="reviewer-footer">
            <span class="badge-pill">Conselho editorial</span>
            @if($reviewer->city ?? false)
              <span>{{ $reviewer->city }}</span>
            @endif
          </div>
        </article>
      @endforeach
    </section>
  @else
    <p class="empty-state">
      Ainda não há revisores cadastrados no conselho editorial.
    </p>
  @endif
</main>
@endsection
