# Mapa do Sistema – Revista Trivento

Olá, pessoal!

Este documento é um **mapa da arquitetura** do projeto da Revista Trivento.

A ideia é que você consiga responder:

-   “Onde ficam as coisas nesse projeto?”
-   “Se eu quero mexer em tal tela, onde eu procuro?”
-   “Como as rotas, controllers e blades se conectam?”

Não precisa decorar tudo. Use este arquivo como **guia de referência**.

---

## 1. Visão geral: como o Laravel organiza um sistema

O Laravel segue o padrão **MVC**:

-   **M**odel → fala com o banco de dados (tabelas, registros).
-   **V**iew → monta o HTML (telas), usando **Blade**.
-   **C**ontroller → recebe a requisição, processa a lógica e escolhe qual view mostrar.

Fluxo simplificado de uma página:

1. Você acessa uma URL no navegador (por exemplo, `/autor/dashboard`).
2. O Laravel olha nas **rotas** (arquivo `routes/web.php`) e descobre:
    - qual **controller** e qual **método** devem responder.
3. O **controller** faz o trabalho (pega dados do banco, organiza informações…).
4. O controller devolve uma **view Blade**, que vira HTML na tela.

---

## 2. Estrutura geral de pastas

O projeto segue a estrutura padrão do Laravel, com as pastas principais:

-   `app/` – código PHP da aplicação (models, controllers, etc.).
-   `bootstrap/` – inicialização do framework.
-   `config/` – arquivos de configuração.
-   `database/` – migrations, seeders e o banco SQLite.
-   `public/` – ponto de entrada da aplicação (onde o servidor aponta).
-   `resources/` – **views Blade**, CSS/JS não compilados, etc.
-   `routes/` – arquivos de rotas (`web.php`, `api.php`, etc.).
-   `storage/` – logs, arquivos gerados, cache.
-   `tests/` – testes automatizados.

O que mais nos interessa para a disciplina:

-   `app/Http/Controllers` → controllers (lógica das telas).
-   `app/Models` → models (relação com as tabelas).
-   `resources/views` → blades (HTML + Blade).
-   `routes/web.php` → rotas (URLs → controllers).

---

## 3. Perfis e “consoles” do sistema

A Revista Trivento é pensada para vários **tipos de usuário** (papéis):

-   **Admin**
-   **Coordenador**
-   **Revisor**
-   **Autor**

Cada papel tem um “console” (um painel próprio), com layout e rotas específicas.

### 3.1. Onde isso aparece no código

No **backend**, essa separação aparece:

-   No **nome das rotas**: `admin.*`, `coordenador.*`, `revisor.*`, `autor.*`
-   Nos **namespaces dos controllers**:
    -   `App\Http\Controllers\Admin\...`
    -   `App\Http\Controllers\Coordenador\...`
    -   `App\Http\Controllers\Revisor\...`
    -   `App\Http\Controllers\Autor\...`

No **frontend**, aparece nas pastas de **views**:

-   `resources/views/admin/...`
-   `resources/views/coordenador/...`
-   `resources/views/revisor/...`
-   `resources/views/autor/...`
-   E também nos layouts compartilhados em `resources/views/console/...`

### 3.2. Papéis (roles) e permissões

O projeto usa papéis como:

-   `Admin`
-   `Coordenador`
-   `Revisor`
-   `Autor`

Esses papéis são usados em:

-   **Middlewares** das rotas (ex.: `middleware('role:Autor')`).
-   Verificações de permissão (ex.: `$user->can('reviews.view_assigned')`).

Na prática: cada “console” só é acessível por quem tem o papel certo.

---

## 4. Rotas: por onde tudo começa

As rotas web ficam principalmente em:

```text
routes/web.php
```
