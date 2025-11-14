# Guia de Instalação – Projeto Revista Trivento

Olá, pessoal!

Este documento explica **passo a passo** como rodar o projeto **Revista Trivento** na máquina de vocês, desde o clone até o login com usuários de teste.

A ideia é que qualquer pessoa da turma consiga seguir este guia, mesmo que ainda não esteja acostumada com Laravel.

> Se você nunca mexeu com terminal, PHP ou Laravel, vá com calma, leia com atenção e siga na ordem.  
> Travou? Anota o erro, tira print e fala comigo.

---

## 1. Visão geral do projeto

Este projeto é uma aplicação Laravel que simula/planta a base de uma **revista científica** (Revista Trivento), com papéis como:

-   **Admin**
-   **Coordenador**
-   **Revisor**
-   **Autor**

Ao longo da disciplina, vamos desenvolver e testar funcionalidades em cima desse código.

---

## 2. O que você precisa ter instalado

Antes de tentar rodar o projeto, verifique se você tem instalado:

-   **Git**
-   **PHP 8.2 ou superior**
-   **Composer** (gerenciador de dependências do PHP)
-   **Node.js** (com **NPM**)
-   **Um editor de código** (recomendado: VS Code)

> Não vamos usar MySQL/PostgreSQL por padrão. O projeto já está configurado para usar **SQLite**, que é um banco de dados em arquivo, bem mais simples para quem está começando.

Se você não tiver certeza se tem tudo instalado:

-   No terminal, rode:

    ```bash
    php -v
    composer -V
    node -v
    npm -v
    git --version
    ```
