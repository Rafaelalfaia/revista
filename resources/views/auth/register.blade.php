@extends('site.layout')

@section('title','Cadastro · Revista Trivento')

@php
    $loginMode = old('login_mode', 'email'); // 'email' ou 'cpf'
@endphp

@push('head')
<style>
  .auth-shell{
    max-width:72rem;
    margin:0 auto;
    padding:3rem 1.25rem 4rem;
    display:flex;
    justify-content:center;
  }
  @media(min-width:768px){
    .auth-shell{
      padding:4rem 0 5rem;
    }
  }

  .auth-card{
    width:100%;
    max-width:28rem;
    border-radius:1.5rem;
    border:1px solid var(--line);
    background:var(--panel);
    box-shadow:0 22px 55px rgba(15,23,42,.35);
    padding:1.75rem 1.5rem 2.1rem;
    display:flex;
    flex-direction:column;
    gap:1.25rem;
  }

  .auth-logo-wrap{
    display:flex;
    flex-direction:column;
    align-items:center;
    gap:.8rem;
    text-align:center;
  }
  .auth-logo-circle{
    width:4.2rem;
    height:4.2rem;
    border-radius:1.4rem;
    background:var(--panel-2);
    border:1px solid var(--line);
    display:flex;
    align-items:center;
    justify-content:center;
    box-shadow:0 12px 30px rgba(15,23,42,.35);
  }
  .auth-title{
    font-size:1.3rem;
    font-weight:800;
  }
  .auth-sub{
    font-size:.88rem;
    color:var(--muted);
  }

  .google-btn{
    width:100%;
    display:flex;
    align-items:center;
    justify-content:center;
    gap:.5rem;
    padding:.55rem .9rem;
    border-radius:999px;
    border:1px solid var(--line);
    background:var(--panel-2);
    font-size:.85rem;
    font-weight:500;
    text-decoration:none;
    transition:background .15s ease, transform .1s ease, box-shadow .15s ease;
  }
  .google-btn:hover{
    background:var(--panel);
    transform:translateY(-1px);
    box-shadow:0 14px 32px rgba(15,23,42,.45);
  }

  .divider-row{
    position:relative;
    margin:.75rem 0;
  }
  .divider-row::before{
    content:'';
    position:absolute;
    inset:0;
    border-top:1px solid var(--line);
  }
  .divider-label{
    position:relative;
    display:inline-flex;
    padding:0 .6rem;
    margin:0 auto;
    font-size:.7rem;
    text-transform:uppercase;
    letter-spacing:.12em;
    color:var(--muted);
    background:var(--panel);
  }

  .auth-alert{
    border-radius:.85rem;
    border:1px solid rgba(239,68,68,.45);
    background:rgba(248,113,113,.08);
    padding:.5rem .7rem;
    font-size:.78rem;
    color:#fecaca;
  }

  .auth-footer{
    margin-top:.25rem;
    text-align:center;
    font-size:.78rem;
    color:var(--muted);
  }

  .auth-footer a{
    color:var(--brand);
    font-weight:500;
  }

  .auth-toggle{
    display:flex;
    gap:.35rem;
    padding:.25rem;
    border-radius:999px;
    background:var(--panel-2);
    border:1px solid var(--line);
    font-size:.75rem;
  }
  .auth-toggle-btn{
    flex:1;
    border-radius:999px;
    padding:.25rem .6rem;
    border:none;
    background:transparent;
    color:var(--muted);
    font-weight:600;
    cursor:pointer;
    transition:background .15s ease, color .15s ease, box-shadow .15s ease;
  }
  .auth-toggle-btn.is-active{
    background:var(--brand);
    color:#fff;
    box-shadow:0 10px 24px rgba(236,72,153,.55);
  }

  .logo-light{display:block;}
  .logo-dark{display:none;}

  html.dark .logo-light,
  body.dark .logo-light,
  [data-theme="dark"] .logo-light{display:none;}

  html.dark .logo-dark,
  body.dark .logo-dark,
  [data-theme="dark"] .logo-dark{display:block;}
</style>
@endpush

@section('content')
<main class="auth-shell">
  <section class="auth-card">
    {{-- topo --}}
    <div class="auth-logo-wrap">
      <div class="auth-logo-circle">
        <img src="{{ asset('images/logo.png') }}"
             alt="Revista Trivento"
             class="logo-light w-10 h-10 object-contain">
        <img src="{{ asset('images/logo1.png') }}"
             alt="Revista Trivento"
             class="logo-dark w-10 h-10 object-contain">
      </div>
      <div>
        <h1 class="auth-title">Criar conta</h1>
        <p class="auth-sub">
          Crie seu acesso para enviar submissões e acompanhar a Revista Trivento.
        </p>
      </div>
    </div>

    {{-- Google --}}
    <div>
      <a href="{{ route('auth.google') }}" class="google-btn">
        <svg class="w-4 h-4" viewBox="0 0 24 24">
          <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
          <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
          <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
          <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
        </svg>
        <span>Continuar com Google</span>
      </a>
    </div>

    @if($errors->has('google'))
      <div class="auth-alert">
        {{ $errors->first('google') }}
      </div>
    @endif

    {{-- divisor --}}
    <div class="divider-row">
      <div class="flex justify-center">
        <span class="divider-label">ou preencha seus dados</span>
      </div>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
      @csrf

      <input type="hidden" name="login_mode" id="login_mode" value="{{ $loginMode }}">

      {{-- toggle e-mail / cpf --}}
      <div class="auth-toggle">
        <button type="button"
                class="auth-toggle-btn {{ $loginMode === 'email' ? 'is-active' : '' }}"
                data-login-mode="email">
          Usar e-mail
        </button>
        <button type="button"
                class="auth-toggle-btn {{ $loginMode === 'cpf' ? 'is-active' : '' }}"
                data-login-mode="cpf">
          Usar CPF
        </button>
      </div>

      {{-- Nome --}}
      <div>
        <x-input-label for="name" value="Nome completo" class="font-semibold text-[var(--text)] text-xs uppercase tracking-[.14em]" />
        <x-text-input
          id="name"
          type="text"
          name="name"
          class="block mt-1 w-full rounded-xl border-[var(--line)] bg-[var(--panel-2)] text-[var(--text)] focus:border-[var(--brand)] focus:ring-[var(--brand)] text-sm"
          :value="old('name')"
          required
          autofocus
          autocomplete="name"
          placeholder="Digite seu nome completo"
        />
        <x-input-error :messages="$errors->get('name')" class="mt-1" />
      </div>

      {{-- E-mail --}}
      <div id="box-email" @if($loginMode === 'cpf') style="display:none" @endif>
        <x-input-label for="email" value="E-mail" class="font-semibold text-[var(--text)] text-xs uppercase tracking-[.14em]" />
        <x-text-input
          id="email"
          type="email"
          name="email"
          class="block mt-1 w-full rounded-xl border-[var(--line)] bg-[var(--panel-2)] text-[var(--text)] focus:border-[var(--brand)] focus:ring-[var(--brand)] text-sm"
          :value="old('email')"
          autocomplete="email"
          placeholder="Digite seu e-mail"
        />
        <x-input-error :messages="$errors->get('email')" class="mt-1" />
      </div>

      {{-- CPF --}}
      <div id="box-cpf" @if($loginMode === 'email') style="display:none" @endif>
        <x-input-label for="cpf" value="CPF" class="font-semibold text-[var(--text)] text-xs uppercase tracking-[.14em]" />
        <x-text-input
          id="cpf"
          type="text"
          name="cpf"
          class="block mt-1 w-full rounded-xl border-[var(--line)] bg-[var(--panel-2)] text-[var(--text)] focus:border-[var(--brand)] focus:ring-[var(--brand)] text-sm"
          :value="old('cpf')"
          maxlength="14"
          placeholder="000.000.000-00"
        />
        <x-input-error :messages="$errors->get('cpf')" class="mt-1" />
      </div>

      {{-- Senha --}}
      <div>
        <x-input-label for="password" :value="__('Senha')" class="font-semibold text-[var(--text)] text-xs uppercase tracking-[.14em]" />
        <x-text-input
          id="password"
          type="password"
          name="password"
          class="block mt-1 w-full rounded-xl border-[var(--line)] bg-[var(--panel-2)] text-[var(--text)] focus:border-[var(--brand)] focus:ring-[var(--brand)] text-sm"
          required
          autocomplete="new-password"
          placeholder="Crie uma senha"
        />
        <x-input-error :messages="$errors->get('password')" class="mt-1" />
      </div>

      {{-- Confirmar senha --}}
      <div>
        <x-input-label for="password_confirmation" :value="__('Confirmar senha')" class="font-semibold text-[var(--text)] text-xs uppercase tracking-[.14em]" />
        <x-text-input
          id="password_confirmation"
          type="password"
          name="password_confirmation"
          class="block mt-1 w-full rounded-xl border-[var(--line)] bg-[var(--panel-2)] text-[var(--text)] focus:border-[var(--brand)] focus:ring-[var(--brand)] text-sm"
          required
          autocomplete="new-password"
          placeholder="Repita a senha"
        />
        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
      </div>

      <div class="pt-1">
        <button type="submit"
                class="w-full inline-flex justify-center items-center gap-2 bg-[var(--brand)] hover:brightness-110 text-white font-semibold py-2.5 rounded-xl text-sm shadow-lg shadow-[var(--brand)]/40 transition-all duration-200 hover:scale-[1.01] focus:outline-none focus:ring-2 focus:ring-[var(--brand)] focus:ring-offset-2 focus:ring-offset-[var(--bg)]">
          Criar conta
        </button>
      </div>
    </form>

    <div class="auth-footer">
      Já tem conta?
      <a href="{{ route('login') }}">Entrar</a>
    </div>
  </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const modeInput = document.getElementById('login_mode');
  const buttons = document.querySelectorAll('[data-login-mode]');
  const boxEmail = document.getElementById('box-email');
  const boxCpf = document.getElementById('box-cpf');
  const cpfInput = document.getElementById('cpf');

  function setMode(mode) {
    modeInput.value = mode;
    buttons.forEach(btn => {
      btn.classList.toggle('is-active', btn.dataset.loginMode === mode);
    });

    if (mode === 'email') {
      boxEmail.style.display = '';
      boxCpf.style.display = 'none';
    } else {
      boxEmail.style.display = 'none';
      boxCpf.style.display = '';
    }
  }

  buttons.forEach(btn => {
    btn.addEventListener('click', () => setMode(btn.dataset.loginMode));
  });

  if (cpfInput) {
    cpfInput.addEventListener('input', function () {
      let v = this.value.replace(/\D/g, '');
      if (v.length > 11) v = v.slice(0, 11);

      if (v.length > 9) {
        this.value = v.replace(/(\d{3})(\d{3})(\d{3})(\d{0,2})/, '$1.$2.$3-$4');
      } else if (v.length > 6) {
        this.value = v.replace(/(\d{3})(\d{3})(\d{0,3})/, '$1.$2.$3');
      } else if (v.length > 3) {
        this.value = v.replace(/(\d{3})(\d{0,3})/, '$1.$2');
      } else {
        this.value = v;
      }
    });
  }

  setMode(modeInput.value || 'email');
});
</script>
@endsection
