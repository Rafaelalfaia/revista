@extends('site.layout')

@section('title','Sobre a revista · Revista Trivento')

@push('head')
<style>
  .page-shell{max-width:72rem;margin:0 auto;padding:2.5rem 1.25rem 3.5rem}
  @media(min-width:768px){
    .page-shell{padding:3rem 0 4rem}
  }

  .grid-main{display:grid;grid-template-columns:1.1fr .9fr;gap:2rem}
  @media(max-width:1023.98px){
    .grid-main{grid-template-columns:1fr}
  }

  .card-main{
    border-radius:1.5rem;
    border:1px solid var(--line);
    background:
      radial-gradient(circle at top left,rgba(244,114,182,.20),transparent 55%),
      radial-gradient(circle at bottom right,rgba(236,72,153,.15),transparent 55%),
      var(--panel);
    padding:1.8rem 1.7rem;
  }

  .card-header{display:flex;gap:1.2rem;align-items:flex-start;margin-bottom:1.4rem}
  .card-icon{
    width:2.8rem;height:2.8rem;border-radius:999px;
    display:flex;align-items:center;justify-content:center;
    background:rgba(244,114,182,.15);
    color:#f9a8d4;font-size:1.6rem;flex-shrink:0;
  }
  .card-title{font-size:1.5rem;font-weight:800;margin-bottom:.25rem}
  @media(min-width:768px){
    .card-title{font-size:1.7rem}
  }
  .card-sub{font-size:.92rem;color:var(--muted);max-width:40rem}

  .section-block{margin-top:1.6rem}
  .section-eyebrow{font-size:.7rem;color:var(--muted);text-transform:uppercase;letter-spacing:.08em;margin-bottom:.15rem}
  .section-title{font-size:1.05rem;font-weight:700;margin-bottom:.4rem}
  .section-text{font-size:.9rem;color:var(--text);line-height:1.7;text-align:justify}
  .section-text p{margin-bottom:.9rem}
  .section-text ul{list-style:disc;padding-left:1.3rem;margin-bottom:.7rem}
  .section-text li{margin-bottom:.22rem}

  .side-card{
    border-radius:1.4rem;
    border:1px solid var(--line);
    background:var(--panel);
    padding:1.5rem 1.4rem;
    font-size:.86rem;
    color:var(--muted);
    display:flex;
    flex-direction:column;
    gap:1.1rem;
  }
  .side-title{font-size:1rem;font-weight:700;color:var(--text);margin-bottom:.35rem}
  .side-text{line-height:1.6}

  .team-highlight{
    border-radius:1.3rem;
    border:1px solid rgba(236,72,153,.5);
    background:
      radial-gradient(circle at top left,rgba(236,72,153,.20),transparent 55%),
      var(--panel-2, var(--panel));
    padding:1rem .95rem 1.1rem;
  }
  .team-title{font-size:.95rem;font-weight:700;color:#f9a8d4;margin-bottom:.3rem}
  .team-text{font-size:.83rem;color:var(--muted);margin-bottom:.6rem}

  .cta-link{
    display:inline-flex;
    align-items:center;
    gap:.25rem;
    font-weight:600;
    font-size:.88rem;
    color:#fb7185;
  }
  .cta-link span:last-child{transition:transform .16s ease-out}
  .cta-link:hover span:last-child{transform:translateX(2px)}
</style>
@endpush

@section('content')
<main class="page-shell">
  <div class="grid-main">
    <section class="card-main">
      <div class="card-header">
        <div class="card-icon">📖</div>
        <div>
          <h1 class="card-title">Sobre a revista</h1>
          <p class="card-sub">
            A Revista Trivento é um periódico científico vinculado à Trivento Educação, dedicado à
            divulgação de pesquisas, experiências e inovações nas áreas de educação, tecnologia e sociedade.
          </p>
        </div>
      </div>

      <div class="section-block">
        <div class="section-eyebrow">Missão e escopo</div>
        <h2 class="section-title">Ciência acessível e conectada ao território</h2>
        <div class="section-text">
          <p>
            A revista nasce com o propósito de aproximar produção acadêmica, escolas, organizações e comunidade,
            valorizando pesquisas que dialogam com a realidade amazônica e com outros contextos brasileiros.
            Publica artigos originais, revisões, relatos de experiência e estudos de caso voltados para temas
            como metodologias ativas, inovação educacional, tecnologias digitais, políticas públicas, inclusão
            e formação profissional.
          </p>
          <p>
            O periódico adota modelo de acesso aberto, favorecendo que estudantes, docentes e profissionais
            encontrem gratuitamente resultados de pesquisas e materiais de apoio à prática educacional.
          </p>
        </div>
      </div>

      <div class="section-block">
        <div class="section-eyebrow">Projeto de extensão</div>
        <h2 class="section-title">Revista Trivento como ação formativa</h2>
        <div class="section-text">
          <p>
            A criação da Revista Trivento integra o Projeto de Extensão <strong>“Revista Trivento”</strong>,
            coordenado pelo professor <strong>Rafael de Souza Alfaia</strong>, da
            <strong>Faculdade Serra Dourada de Altamira</strong>. O projeto foi desenvolvido em parceria com
            estudantes de diferentes cursos e campi, articulando ensino, pesquisa e extensão em torno da
            construção de um periódico científico digital.
          </p>
          <p>
            Ao longo do projeto, os participantes atuaram em frentes de desenvolvimento de sistema,
            normalização técnica, comunicação científica e curadoria de conteúdos, experimentando na prática
            o ciclo completo de produção e gestão de uma revista acadêmica.
          </p>
        </div>
      </div>

      <div class="section-block">
        <div class="section-eyebrow">Políticas editoriais</div>
        <h2 class="section-title">Compromisso com a qualidade e a ética</h2>
        <div class="section-text">
          <p>
            A revista adota avaliação por pares e segue boas práticas de publicação científica, com atenção
            à transparência do processo editorial, ao respeito às normas éticas de pesquisa e à integridade
            dos dados apresentados. O Conselho Editorial é composto por docentes e pesquisadores que contribuem
            voluntariamente para a qualificação dos manuscritos recebidos.
          </p>
          <p>
            As normas para autores, políticas de direitos autorais, diretrizes de avaliação e demais documentos
            institucionais estão disponíveis nas seções específicas do site, garantindo clareza e previsibilidade
            para todos os envolvidos.
          </p>
        </div>
      </div>

      <div class="section-block">
        <div class="section-eyebrow">Tecnologia e design</div>
        <h2 class="section-title">Experiência digital em formato de app</h2>
        <div class="section-text">
          <p>
            Toda a plataforma da Revista Trivento foi concebida com foco em usabilidade e experiência do usuário,
            inspirada em aplicações PWA (Progressive Web App). A interface responsiva, a leitura otimizada para
            dispositivos móveis e os recursos de navegação foram pensados para aproximar o formato de revista
            científica da linguagem dos aplicativos contemporâneos.
          </p>
        </div>
      </div>
    </section>

    <aside class="side-card">
      <div>
        <h2 class="side-title">A revista e a Trivento Educação</h2>
        <p class="side-text">
          A revista integra o ecossistema de iniciativas da Trivento Educação, contribuindo para a formação
          de pesquisadores, professores e estudantes, e criando um espaço permanente para a circulação de
          ideias, resultados de projetos e práticas inovadoras.
        </p>
      </div>

      <div>
        <h2 class="side-title">Extensão universitária em rede</h2>
        <p class="side-text">
          A construção da revista envolveu estudantes de diferentes instituições parceiras, em especial os
          campi da Faculdade Serra Dourada (Altamira e Itabirito) e a Faculdade Alis (Lorena), fortalecendo
          o trabalho colaborativo e o desenvolvimento de competências em ambientes reais de projeto.
        </p>
      </div>

      <div class="team-highlight">
        <div class="team-title">Equipe de criação da revista</div>
        <p class="team-text">
          Conheça o professor coordenador e os estudantes que participaram do desenvolvimento,
          normalização técnica e comunicação da Revista Trivento.
        </p>
        <a href="{{ route('site.journal.team') }}" class="cta-link">
          <span>Ver equipe completa</span>
          <span>→</span>
        </a>
      </div>
    </aside>
  </div>
</main>
@endsection
