@extends('site.layout')

@section('title','Equipe de criação da revista · Revista Trivento')

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
    padding:1.8rem 1.7rem;
    margin-bottom:2.2rem;
  }
  .header-title{font-size:1.6rem;font-weight:800;margin-bottom:.3rem}
  @media(min-width:768px){
    .header-title{font-size:1.8rem}
  }
  .header-sub{font-size:.92rem;color:var(--muted);max-width:46rem;line-height:1.7}

  .prof-card{
    border-radius:1.4rem;
    border:1px solid rgba(236,72,153,.6);
    background:var(--panel);
    padding:1.4rem 1.4rem 1.3rem;
    display:flex;
    gap:1rem;
    align-items:flex-start;
    margin-bottom:2rem;
  }
  .prof-avatar{
    width:3.1rem;height:3.1rem;border-radius:999px;
    background:radial-gradient(circle at top left,rgba(236,72,153,.25),transparent 60%),#020617;
    display:flex;align-items:center;justify-content:center;
    font-weight:800;font-size:1.3rem;color:#fecaca;flex-shrink:0;
  }
  .prof-name{font-size:1.05rem;font-weight:700}
  .prof-role{font-size:.8rem;color:var(--muted);margin-bottom:.25rem}
  .prof-extra{font-size:.83rem;color:var(--muted);line-height:1.6}

  .team-grid{
    display:grid;
    grid-template-columns:repeat(3,minmax(0,1fr));
    gap:1.4rem;
  }
  @media(max-width:1023.98px){
    .team-grid{grid-template-columns:repeat(2,minmax(0,1fr))}
  }
  @media(max-width:639.98px){
    .team-grid{grid-template-columns:1fr}
  }

  .group-card{
    border-radius:1.4rem;
    border:1px solid var(--line);
    background:var(--panel);
    padding:1.3rem 1.2rem 1.25rem;
    font-size:.85rem;
  }
  .group-title-eyebrow{
    font-size:.75rem;
    text-transform:uppercase;
    letter-spacing:.09em;
    color:var(--muted);
    margin-bottom:.15rem;
  }
  .group-title{font-size:1rem;font-weight:700;margin-bottom:.5rem;color:var(--text)}

  .campus-block{margin-top:.6rem}
  .campus-name{font-size:.8rem;font-weight:600;color:var(--muted);margin-bottom:.25rem}

  .names-list{line-height:1.5}
  .names-list p{margin-bottom:.2rem}
  .names-list strong{color:#f9a8d4}

  .divider{height:1px;background:var(--line);margin:1.4rem 0}
</style>
@endpush

@section('content')
<main class="page-shell">
  <section class="header-card">
    <h1 class="header-title">Equipe de criação da Revista Trivento</h1>
    <p class="header-sub">
      A revista foi desenvolvida no âmbito do Projeto de Extensão <strong>“Revista Trivento”</strong>,
      coordenado pelo professor <strong>Rafael de Souza Alfaia</strong>, da Faculdade Serra Dourada de Altamira.
      A seguir, apresentamos o professor responsável e as equipes de Desenvolvimento, Normas Técnicas e
      Comunicação, compostas por estudantes de diferentes unidades.
    </p>
  </section>

  <section class="prof-card">
    <div class="prof-avatar">R</div>
    <div>
      <div class="prof-name">Prof. Rafael de Souza Alfaia</div>
      <div class="prof-role">Coordenador do Projeto de Extensão Revista Trivento</div>
      <div class="prof-extra">
        Faculdade Serra Dourada – Altamira. Desenvolvedor Sênior / Engenheiro de Computação e responsável
        pela orientação técnica e pedagógica de todo o projeto da revista e de sua plataforma digital.
      </div>
    </div>
  </section>

  <section class="team-grid">
    {{-- Desenvolvimento --}}
    <article class="group-card">
      <div class="group-title-eyebrow">Equipe</div>
      <h2 class="group-title">Desenvolvimento</h2>

      <div class="campus-block">
        <div class="campus-name">Desenvolvimento – Equipe geral</div>
        <div class="names-list">
          <p><strong>Murilo Oliveira Moschen - Líder</strong></p>
          <p>Henzo Hermes Thomaz</p>
          <p>João Pedro Pereira Costa dos Santos</p>
          <p>João Vitor de Freitas Brigido</p>
          <p>José Adriano Almeida De Brito</p>
          <p>José Carlito Alves Bôto Júnior</p>
          <p>Kaique Marley Carbone</p>
          <p>Lucas Vinicius Almeida de Jesus</p>
          <p>Maria Eduarda Santos de Souza</p>
          <p>Marianna Lúcia Farias Monteiro</p>
          <p>Murilo Estevão Sampaio Rosa</p>
          <p>Nicoli Nunes Ribeiro</p>
          <p>Rodolfo Alves Vitorino Filho</p>
          <p>Ruan Matheus Gonzaga Reis</p>
          <p>Vitor Gabriel de Almeida Reis Sene</p>
          <p>Yasmin Nicolly Carvalho Rios</p>
        </div>
      </div>
    </article>

    {{-- Normas Técnicas --}}
    <article class="group-card">
      <div class="group-title-eyebrow">Equipe</div>
      <h2 class="group-title">Normas Técnicas</h2>

      <div class="campus-block">
        <div class="campus-name">Faculdade Serra Dourada – Altamira</div>
        <div class="names-list">
          <p><strong>Samiriam Bitencourt - Líder</strong></p>
          <p>Caio Henrique Santos Tambara de Camargo</p>
          <p>Eduardo Pires Martins</p>
          <p>Hitalo da Silva Sousa</p>
          <p>Itauana Camilly Silva Martins</p>
          <p>Kerlon Pereira Bessa</p>
          <p>Lillian Corrêa Savorette Passarelli</p>
          <p>Lucas Crispim Santos de Menezes</p>
          <p>Luciana Resendes Holanda</p>
          <p>Luciana Silva dos Santos</p>
          <p>Paulo Roberto Fontes da Silva</p>
          <p>Yasmim Lima Fonseca</p>
        </div>
      </div>

      <div class="divider"></div>

      <div class="campus-block">
        <div class="campus-name">Faculdade Serra Dourada – Itabirito</div>
        <div class="names-list">
          <p>Ana Carolina Felipe Camilo</p>
          <p>Ana Júlia Pereira Menezes</p>
          <p>Iris Reis Ramos de Lima</p>
          <p>Júnior Edson da Silva</p>
          <p>Matheus Almeida Xavier Lopes</p>
          <p>Raissa Naiara Xavier</p>
        </div>
      </div>

      <div class="divider"></div>

      <div class="campus-block">
        <div class="campus-name">Faculdade Alis – Lorena</div>
        <div class="names-list">
          <p>Isabeli Pereira Marton</p>
        </div>
      </div>
    </article>

    {{-- Comunicação --}}
    <article class="group-card">
      <div class="group-title-eyebrow">Equipe</div>
      <h2 class="group-title">Comunicação</h2>

      <div class="campus-block">
        <div class="campus-name">Faculdade Serra Dourada – Altamira</div>
        <div class="names-list">
          <p><strong>Juliana Carla Lima Freire - Líder</strong></p>
          <p>Allana da Silva Lima</p>
          <p>Ana Karla Machado Fontinele</p>
          <p>Andreza Coelho da Silva</p>
          <p>Cícera Larissa dos Santos</p>
          <p>Gustavo Ferreira</p>
          <p>Ismael Silva de Souza</p>
          <p>Julio Cesar Faes Brogni</p>
          <p>Kesner Lira de Lima</p>
          <p>Michelle Morais Gomes</p>
          <p>Renee Kaue Santos Tavares</p>
          <p>Taíse Lima de Araújo</p>
        </div>
      </div>

      <div class="divider"></div>

      <div class="campus-block">
        <div class="campus-name">Faculdade Serra Dourada – Itabirito</div>
        <div class="names-list">
          <p><strong>Gabriela Maria F. Marques - Líder</strong></p>
          <p>Adão Gabriel Costa Motta</p>
          <p>Anie Avelino</p>
          <p>Fernando de Castro</p>
          <p>Franciele Barcelos Moraes</p>
          <p>Gabriella Carolina Costa</p>
          <p>Irlaine Aparecida Gonçalves Barçante</p>
          <p>Maria Eduarda Varela de Oliveira Lima</p>
          <p>Stephany Eduarda Cunha Chaves</p>
        </div>
      </div>

      <div class="divider"></div>

      <div class="campus-block">
        <div class="campus-name">Faculdade Alis – Lorena</div>
        <div class="names-list">
          <p>Alécio Diniz dos Santos</p>
          <p>Amanda Maria de Gouvea Santos</p>
          <p>Amanda Maria Lopes Pinto</p>
          <p>Ana Paula Alves de Souza</p>
          <p>Breno Ribeiro da Silva Meirelis de Siqueira</p>
          <p>Débora Ribeiro Alves</p>
          <p>Edvania Maria Lopes da Silva</p>
          <p>Felipe Augusto Santos Lourenço</p>
          <p>Gabrielle Leite Ribeiro de Freitas</p>
          <p>Guilherme Marcelo Teles de Oliveira</p>
          <p>Isabelle de Paula Fernandes</p>
          <p>Jade Aparecida Matias Lemes Ramos</p>
          <p>João Pedro Codelo Leite</p>
          <p>Juliani de Souza Correia Burgarelli</p>
          <p>Lavínia de Oliveira Ramos</p>
          <p>Leandra Accioly Maria Ribeiro</p>
          <p>Lucas Ricardo Nunes Honorato</p>
          <p>Maria Cecilia Antunes Meira</p>
          <p>Maria Clara de Souza Correa</p>
          <p>Maria das Graças do Prado Goulart</p>
          <p>Maria Eduarda Ferreira de Araujo</p>
          <p>Maria Julia Oliveira da Silva Miranda</p>
          <p>Mirela dos Santos Peres</p>
          <p>Natália Cristina Elias da Silva</p>
          <p>Paola Brenda Cristina dos Santos</p>
          <p>Ramon Gonçalves de Oliveira Sirico</p>
          <p>Renata da Silva Corrêa</p>
        </div>
      </div>
    </article>
  </section>
</main>
@endsection
