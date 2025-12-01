@extends('site.layout')

@section('title','Diretrizes para autores · Revista Trivento')

@push('head')
<style>
  .page-shell{max-width:72rem;margin:0 auto;padding:2.5rem 1.25rem 3.5rem}
  @media(min-width:768px){
    .page-shell{padding:3rem 0 4rem}
  }

  .page-grid{display:grid;grid-template-columns:1.1fr .9fr;gap:2rem}
  @media(max-width:1023.98px){
    .page-grid{grid-template-columns:1fr}
  }

  .card-main{
    border-radius:1.5rem;
    border:1px solid var(--line);
    background:
      radial-gradient(circle at top left,rgba(244,114,182,.20),transparent 55%),
      radial-gradient(circle at bottom right,rgba(236,72,153,.15),transparent 55%),
      var(--panel);
    padding:1.75rem 1.6rem;
  }

  .card-main-header{display:flex;align-items:flex-start;gap:1rem;margin-bottom:1.25rem}
  .card-icon{
    width:2.5rem;height:2.5rem;border-radius:999px;
    display:flex;align-items:center;justify-content:center;
    background:rgba(244,114,182,.12);
    color:#f9a8d4;font-size:1.3rem;flex-shrink:0;
  }

  .card-title{font-size:1.4rem;font-weight:800;margin-bottom:.2rem}
  .card-sub{font-size:.9rem;color:var(--muted)}

  .pill-row{display:flex;flex-wrap:wrap;gap:.4rem;margin-top:1rem}
  .pill{
    font-size:.7rem;
    text-transform:uppercase;
    letter-spacing:.09em;
    padding:.22rem .6rem;
    border-radius:999px;
    border:1px solid rgba(148,163,184,.5);
    color:var(--muted);
  }

  .section-block{margin-top:1.75rem}
  .section-title{font-size:1.05rem;font-weight:700;margin-bottom:.4rem}
  .section-eyebrow{font-size:.7rem;color:var(--muted);text-transform:uppercase;letter-spacing:.08em;margin-bottom:.15rem}

  .section-text{font-size:.9rem;color:var(--text);line-height:1.7;text-align:justify}
  .section-text p{margin-bottom:.9rem}
  .section-text ul{list-style:disc;padding-left:1.2rem;margin-bottom:.7rem}
  .section-text li{margin-bottom:.2rem}

  .side-card{
    border-radius:1.4rem;
    border:1px solid var(--line);
    background:var(--panel);
    padding:1.4rem 1.3rem;
    font-size:.85rem;
    color:var(--muted);
  }
  .side-card-title{font-size:.95rem;font-weight:700;color:var(--text);margin-bottom:.4rem}
  .side-list{margin-top:.5rem}
  .side-list li{margin-bottom:.35rem}

  .cta-link{
    display:inline-flex;
    align-items:center;
    gap:.25rem;
    margin-top:1rem;
    font-weight:600;
    font-size:.9rem;
    color:#fb7185;
  }
  .cta-link span{transition:transform .16s ease-out}
  .cta-link:hover span{transform:translateX(2px)}
</style>
@endpush

@section('content')
<main class="page-shell">
  <div class="page-grid">
    <section class="card-main">
      <div class="card-main-header">
        <div class="card-icon">
          📄
        </div>
        <div>
          <h1 class="card-title">Para autores</h1>
          <p class="card-sub">
            Diretrizes de submissão, formatação dos manuscritos e orientações sobre o fluxo editorial da Revista Trivento.
          </p>
        </div>
      </div>

      <div class="pill-row">
        <span class="pill">Submissões on-line</span>
        <span class="pill">Avaliação por pares</span>
        <span class="pill">Normas ABNT</span>
        <span class="pill">Acesso aberto</span>
      </div>

      <div class="section-block">
        <div class="section-eyebrow">1. Antes de submeter</div>
        <h2 class="section-title">Escopo e originalidade</h2>
        <div class="section-text">
          <p>
            A Revista Trivento publica resultados de pesquisas, relatos de experiência e revisões com foco em
            educação, tecnologia, inovação e áreas correlatas. Os manuscritos devem ser originais, inéditos e
            não podem estar simultaneamente em avaliação em outro periódico, evento ou livro.
          </p>
          <p>
            Recomenda-se que os autores verifiquem se o tema, a abordagem metodológica e os resultados dialogam
            com edições recentes da revista, garantindo aderência ao escopo e contribuição ao campo científico.
          </p>
        </div>
      </div>

      <div class="section-block">
        <div class="section-eyebrow">2. Tipos de manuscritos</div>
        <h2 class="section-title">Categorias aceitas</h2>
        <div class="section-text">
          <ul>
            <li><strong>Artigo original</strong> – apresenta resultados inéditos de pesquisas empíricas ou teóricas.</li>
            <li><strong>Comunicação breve</strong> – relatos curtos de estudos em andamento, produtos educacionais ou evidências preliminares.</li>
            <li><strong>Revisão de narrativa</strong> – síntese crítica de literatura sobre determinado tema.</li>
            <li><strong>Revisão sistemática</strong> – estudos que seguem protocolo explícito de busca, seleção e análise de publicações.</li>
            <li><strong>Estudo/relato de caso</strong> – descrição aprofundada de uma experiência, contexto ou intervenção específica.</li>
            <li><strong>Relato técnico/experiência</strong> – descrição de práticas, projetos, softwares, materiais ou processos com potencial de replicação.</li>
          </ul>
        </div>
      </div>

      <div class="section-block">
        <div class="section-eyebrow">3. Normas de formatação</div>
        <h2 class="section-title">Estrutura e apresentação do texto</h2>
        <div class="section-text">
          <p>
            O manuscrito deve ser enviado em formato editável (por exemplo, <em>.docx</em>), com páginas em tamanho A4,
            margens de 2,5&nbsp;cm, espaçamento 1,5 e fonte padrão (como Times New Roman ou similar) em corpo 12.
          </p>
          <p>
            Recomenda-se seguir a estrutura básica: título, resumo, palavras-chave, introdução, métodos, resultados,
            discussão, conclusões e referências. Para relatos de experiência e outros formatos, a organização pode ser
            adaptada, desde que haja clareza, coerência e fundamentação teórica.
          </p>
          <p>
            As referências, citações no texto, quadros, tabelas e figuras devem seguir as normas vigentes da ABNT
            ou outro padrão indicado nas diretrizes completas da revista.
          </p>
        </div>
      </div>

      <div class="section-block">
        <div class="section-eyebrow">4. Avaliação</div>
        <h2 class="section-title">Processo editorial e revisão por pares</h2>
        <div class="section-text">
          <p>
            Após a submissão, o manuscrito passa por uma triagem inicial do Conselho Editorial quanto à adequação
            ao escopo, à conformidade com as normas e à qualidade geral do texto. Trabalhos que não atendem a requisitos
            mínimos podem ser devolvidos aos autores para ajustes ou rejeitados nesta etapa.
          </p>
          <p>
            Os manuscritos aprovados na triagem seguem para avaliação por pares, em regime de revisão simples-cega
            ou dupla-cega, conforme definido pela revista. Os pareceristas analisam a relevância do tema, o rigor
            metodológico, a consistência das análises e a contribuição científica do trabalho.
          </p>
        </div>
      </div>

      <div class="section-block">
        <div class="section-eyebrow">5. Ética e boas práticas</div>
        <h2 class="section-title">Responsabilidades dos autores</h2>
        <div class="section-text">
          <p>
            É responsabilidade dos autores garantir a veracidade dos dados apresentados, o respeito às normas éticas
            de pesquisa envolvendo seres humanos, a obtenção de autorizações quando necessárias e a adequada
            citação de todas as fontes utilizadas.
          </p>
          <p>
            A revista não admite plágio, autoplágio excessivo ou manipulação indevida de dados. Casos suspeitos
            podem resultar em rejeição do manuscrito ou retratação, em conformidade com boas práticas internacionais
            de publicação científica.
          </p>
        </div>
      </div>

      <div class="section-block">
        <div class="section-eyebrow">6. Submissão on-line</div>
        <h2 class="section-title">Passo a passo para enviar seu trabalho</h2>
        <div class="section-text">
          <ul>
            <li>Faça seu cadastro como autor na plataforma da revista.</li>
            <li>Atualize seu perfil com afiliação institucional, e-mail e ORCID, quando houver.</li>
            <li>Acesse o menu de submissão e selecione o tipo de manuscrito adequado ao seu texto.</li>
            <li>Preencha os metadados solicitados (título, resumo, palavras-chave, área temática, etc.).</li>
            <li>Envie o arquivo do manuscrito, garantindo que não haja identificação de autoria no corpo do texto quando solicitado.</li>
            <li>Confirme a submissão e acompanhe o andamento do processo editorial pelo painel do autor.</li>
          </ul>
          <p>
            Em caso de dúvidas, consulte o manual detalhado de normalização disponibilizado pela revista
            ou entre em contato com a editoria científica.
          </p>
        </div>
      </div>

      <a href="{{ route('autor.submissions.create') }}" class="cta-link">
        <span>Iniciar uma submissão</span>
        <span>→</span>
      </a>
    </section>

    <aside class="side-card">
      <h2 class="side-card-title">Checklist rápido</h2>
      <p>
        Antes de concluir o envio, confirme se o seu manuscrito atende aos pontos abaixo.
      </p>
      <ul class="side-list list-disc pl-4">
        <li>O texto está dentro do escopo da revista.</li>
        <li>O arquivo segue o modelo e as normas indicadas.</li>
        <li>Não há identificação dos autores em arquivos destinados à revisão cega.</li>
        <li>As referências estão completas e consistentes.</li>
        <li>Todos os coautores estão cientes da submissão.</li>
      </ul>

      <h3 class="side-card-title mt-6">Suporte ao autor</h3>
      <p>
        Para esclarecimentos sobre formatação, política editorial ou uso da plataforma, a equipe da revista
        pode ser contatada pelos canais oficiais informados na página de contato.
      </p>
    </aside>
  </div>
</main>
@endsection
