{{-- resources/views/auth/login.blade.php --}}
@extends('site.layout')

@section('title','Entrar · Revista Trivento')

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
    font-size:1.35rem;
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
    {{-- topo: logo + título --}}
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
        <h1 class="auth-title">Revista Trivento</h1>
        <p class="auth-sub">Acesse sua conta para continuar</p>
      </div>
    </div>

    {{-- botão Google --}}
    <div>
      <a href="{{ route('auth.google') }}" class="google-btn">
        <svg class="w-4 h-4" viewBox="0 0 24 24">
          <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
          <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
          <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
          <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
        </svg>
        <span>Entrar com Google</span>
      </a>
    </div>

    {{-- erro de login com Google --}}
    @if($errors->has('google'))
      <div class="auth-alert">
        {{ $errors->first('google') }}
      </div>
    @endif

    {{-- status de sessão (senha resetada etc.) --}}
    <x-auth-session-status class="mb-1" :status="session('status')" />

    {{-- divisor --}}
    <div class="divider-row">
      <div class="flex justify-center">
        <span class="divider-label">ou continue com e-mail ou CPF</span>
      </div>
    </div>

    {{-- formulário de login --}}
    <form method="POST" action="{{ route('login') }}" class="space-y-4">
      @csrf

      {{-- E-mail ou CPF --}}
      <div>
        <x-input-label for="email" value="E-mail ou CPF" class="font-semibold text-[var(--text)] text-xs uppercase tracking-[.14em]" />
        <x-text-input
          id="email"
          type="text"
          name="email"
          class="block mt-1 w-full rounded-xl border-[var(--line)] bg-[var(--panel-2)] text-[var(--text)] focus:border-[var(--brand)] focus:ring-[var(--brand)] text-sm"
          :value="old('email')"
          required
          autofocus
          autocomplete="username"
          placeholder="Digite seu e-mail institucional ou CPF"
        />
        <x-input-error :messages="$errors->get('email')" class="mt-1" />
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
          autocomplete="current-password"
          placeholder="Digite sua senha"
        />
        <x-input-error :messages="$errors->get('password')" class="mt-1" />
      </div>

      {{-- lembrar / esqueci a senha --}}
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 pt-1">
        <label class="inline-flex items-center text-xs text-[var(--muted)] cursor-pointer select-none">
          <input id="remember_me"
                 type="checkbox"
                 name="remember"
                 class="rounded border-[var(--line)] text-[var(--brand)] shadow-sm focus:ring-[var(--brand)] focus:ring-offset-0">
          <span class="ml-2 font-medium">
            {{ __('Lembrar-me') }}
          </span>
        </label>

        @if (Route::has('password.request'))
          <a href="{{ route('password.request') }}"
             class="text-xs font-medium text-[var(--muted)] hover:text-[var(--brand)] transition-colors">
            {{ __('Esqueceu a senha?') }}
          </a>
        @endif
      </div>

      {{-- botão entrar --}}
      <div class="pt-1">
        <button type="submit"
                class="w-full inline-flex justify-center items-center gap-2 bg-[var(--brand)] hover:brightness-110 text-white font-semibold py-2.5 rounded-xl text-sm shadow-lg shadow-[var(--brand)]/40 transition-all duration-200 hover:scale-[1.01] focus:outline-none focus:ring-2 focus:ring-[var(--brand)] focus:ring-offset-2 focus:ring-offset-[var(--bg)]">
          Entrar na Plataforma
        </button>
      </div>
    </form>

    {{-- link cadastro --}}
    <div class="auth-footer">
      Não tem conta?
      <a href="{{ route('register') }}">Cadastre-se para publicar</a>
    </div>
  </section>
</main>
@endsection
