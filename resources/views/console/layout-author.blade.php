<!DOCTYPE html>
<html lang="pt-BR" class="antialiased">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>@yield('title','Autor · Trivento')</title>

  <script>
    (() => {
      const KEY = 'trv.theme';
      const saved = localStorage.getItem(KEY) || 'auto';
      const prefersDark = matchMedia?.('(prefers-color-scheme: dark)').matches ?? false;
      const dark = saved === 'dark' || (saved === 'auto' && prefersDark);
      document.documentElement.classList.toggle('dark', dark);
      const meta = document.querySelector('meta[name="theme-color"]') || (() => {
        const m = document.createElement('meta');
        m.name = 'theme-color';
        document.head.appendChild(m);
        return m;
      })();
      meta.setAttribute('content', dark ? '#050608' : '#FFFFFF');
      window.__TRV_INITIAL_THEME__ = saved;
    })();
  </script>

  <style>
    :root{
      --bg:#FFFFFF; --text:#0F172A; --muted:#475569;
      --panel:#FFFFFF; --panel-2:#F8FAFC; --line:rgba(15,23,42,.10);
      --brand:#E11D48; --brand-700:#BE123C; --chip:rgba(225,29,72,.08);
      --glass:rgba(255,255,255,.78); --glass-line:rgba(15,23,42,.10);
    }
    .dark{
      --bg:#050608; --text:#E5E7EB; --muted:#94A3B8;
      --panel:#0F1412; --panel-2:#0B0F0D; --line:rgba(255,255,255,.08);
      --brand:#E11D48; --brand-700:#BE123C; --chip:rgba(225,29,72,.12);
      --glass:rgba(8,10,11,.84); --glass-line:rgba(255,255,255,.08);
    }

    html,body{
      background:radial-gradient(circle at top,var(--panel-2) 0,transparent 55%),var(--bg);
      color:var(--text);
      height:auto;
      min-height:100%;
      overflow-y:auto;
    }

    .panel{
      background:var(--panel);
      border-color:var(--line);
      overflow:visible;
      border-radius:1.2rem;
    }
    .panel-2{
      background:var(--panel-2);
      border-color:var(--line);
      border-radius:1.4rem;
    }

    .muted{color:var(--muted)}
    .chip{background:var(--chip)}
    .brand{background:var(--brand)}
    .brand:hover{background:var(--brand-700)}
    .glassbar{
      background:var(--glass);
      -webkit-backdrop-filter:saturate(180%) blur(14px);
      backdrop-filter:saturate(180%) blur(14px);
      border-color:var(--glass-line);
    }

    .console-root{
      min-height:100vh;
      display:flex;
      flex-direction:column;
    }
    .console-body{
      min-height:100vh;
      display:flex;
      width:100%;
    }
    .console-main{
      flex:1 1 auto;
      min-height:100vh;
      overflow-x:hidden;
      width:100%;
    }

    .console-logo-wrap{
      display:flex;
      align-items:center;
      justify-content:center;
      padding:.9rem 0 1.3rem;
    }
    .console-logo-img{
      height:52px;
      width:auto;
      border-radius:1rem;
      padding:.22rem .4rem;
      background:linear-gradient(135deg,rgba(255,255,255,.98),rgba(248,250,252,.9));
      box-shadow:0 14px 34px rgba(15,23,42,.32);
    }
    .dark .console-logo-img{
      background:linear-gradient(135deg,#020617,#020617);
      box-shadow:0 24px 46px rgba(0,0,0,.9);
    }

    .logo-dark,
    .logo-dark-top{
      display:none;
    }
    .dark .logo-light,
    .dark .logo-light-top{
      display:none;
    }
    .dark .logo-dark{
      display:block;
    }
    .dark .logo-dark-top{
      display:inline-block;
    }

    .console-sidebar{
      position:fixed;
      top:12px;
      bottom:12px;
      left:12px;
      width:280px;
      display:none;
    }
    @media(min-width:1024px){
      .console-sidebar{display:block;}
    }
    .console-sidebar-inner{
      position:relative;
      height:100%;
    }
    .console-sidebar-inner::before{
      content:"";
      position:absolute;
      top:-60px;
      left:-40px;
      width:280px;
      height:260px;
      background:radial-gradient(circle at top left,rgba(248,113,133,.35),transparent 70%);
      pointer-events:none;
      z-index:-1;
    }
    .dark .console-sidebar-inner::before{
      background:radial-gradient(circle at top left,rgba(248,113,133,.6),transparent 72%);
    }

    .top-title{
      font-weight:600;
      font-size:.92rem;
      white-space:nowrap;
      overflow:hidden;
      text-overflow:ellipsis;
    }

    .bottom-nav{
      background:var(--glass);
      -webkit-backdrop-filter:saturate(160%) blur(18px);
      backdrop-filter:saturate(160%) blur(18px);
      border-color:var(--glass-line);
      border-top-left-radius:1.25rem;
      border-top-right-radius:1.25rem;
    }

    .bottom-tab{
      display:flex;
      flex-direction:column;
      align-items:center;
      justify-content:center;
      gap:.15rem;
      padding:.45rem 0 .35rem;
      color:var(--muted);
    }
    .bottom-tab span{
      font-size:.7rem;
    }
    .bottom-tab-icon-wrap{
      display:flex;
      align-items:center;
      justify-content:center;
      width:2.25rem;
      height:2.25rem;
      border-radius:999px;
      transition:background .15s, box-shadow .15s, transform .12s;
    }
    .bottom-tab svg{
      width:20px;
      height:20px;
    }
    .bottom-tab-active{
      color:var(--brand);
      font-weight:500;
    }
    .bottom-tab-active .bottom-tab-icon-wrap{
      background:radial-gradient(circle at top,var(--brand) 0,transparent 70%);
      box-shadow:0 0 0 1px rgba(248,250,252,.08),0 10px 20px rgba(225,29,72,.45);
      transform:translateY(-1px);
    }
    .bottom-tab-active::after{
      content:'';
      margin-top:.1rem;
      width:22px;
      height:2px;
      border-radius:999px;
      background:var(--brand);
    }

    @media(min-width:1024px){
      .console-main{
        margin-left:308px;
      }
    }
  </style>

  <meta name="theme-color" content="#050608">
  @vite(['resources/css/app.css','resources/js/app.js'])
  @stack('head')
  <style>[x-cloak]{display:none}</style>
</head>
<body x-data="shell()" x-init="init()" class="console-root">

  <a href="#conteudo" class="sr-only focus:not-sr-only focus:fixed focus:left-3 focus:top-3 focus:z-50 rounded-md px-3 py-2 text-white" style="background:var(--brand)">Ir para o conteúdo</a>

  @php
    $appLogoLight = asset('images/logo.png');
    $appLogoDark  = asset('images/logo1.png');
  @endphp

  <div class="console-body">

    <aside class="console-sidebar" role="navigation" aria-label="Menu lateral">
      <div class="console-sidebar-inner panel border px-4 py-4 flex flex-col">
        <div class="console-logo-wrap">
          <img src="{{ $appLogoLight }}" alt="Logo Revista Trivento" class="console-logo-img logo-light">
          <img src="{{ $appLogoDark }}" alt="Logo Revista Trivento" class="console-logo-img logo-dark">
        </div>

        @php
          $isAutorDash = request()->routeIs('autor.dashboard');
          $isAutorSubs = request()->routeIs('autor.submissions.*');
          $isAutorNoti = request()->routeIs('autor.notifications.*');
          $isPerfil    = request()->routeIs('profile.*');
          $unread = (int) (auth()->user()?->unreadNotifications()->count() ?? 0);
        @endphp

        <nav class="mt-6 space-y-1 text-sm flex-1">
          <x-console.navlink href="{{ Route::has('autor.dashboard') ? route('autor.dashboard') : '#' }}" :active="$isAutorDash">Dashboard</x-console.navlink>

          <div class="mt-4 mb-1 px-2 text-[10px] uppercase tracking-wide muted">Fluxo do Autor</div>
          @if (Route::has('autor.submissions.index'))
            <x-console.navlink href="{{ route('autor.submissions.index') }}" :active="$isAutorSubs">Minhas Submissões</x-console.navlink>
          @else
            <x-console.navlink href="#">Minhas Submissões</x-console.navlink>
          @endif

          @if (Route::has('autor.submissions.create'))
            <x-console.navlink href="{{ route('autor.submissions.create') }}">Nova Submissão</x-console.navlink>
          @else
            <x-console.navlink href="#">Nova Submissão</x-console.navlink>
          @endif

          <div class="mt-4 mb-1 px-2 text-[10px] uppercase tracking-wide muted">Conta</div>
          @if (Route::has('autor.notifications.index'))
            <x-console.navlink href="{{ route('autor.notifications.index') }}" :active="$isAutorNoti">
              <span class="inline-flex items-center gap-2">
                <span>Notificações</span>
                @if($unread > 0)
                  <span class="px-1.5 py-0.5 rounded-md text-[10px] font-medium chip">{{ $unread }}</span>
                @endif
              </span>
            </x-console.navlink>
          @else
            <x-console.navlink href="#">Notificações</x-console.navlink>
          @endif

          @if (Route::has('profile.edit'))
            <x-console.navlink href="{{ route('profile.edit') }}" :active="$isPerfil">Meu Perfil</x-console.navlink>
          @else
            <x-console.navlink href="#">Meu Perfil</x-console.navlink>
          @endif
        </nav>

        <div class="mt-6 border-t pt-4" style="border-color:var(--line);">
          <div class="text-xs muted">Sessão</div>
          @php
            $user = auth()->user();
            $avatar = ($user && !empty($user->avatar_url)) ? $user->avatar_url : asset('images/avatar.png');
          @endphp
          <div class="mt-2 flex items-center gap-3">
            <img src="{{ $avatar }}" class="h-9 w-9 rounded-full object-cover" alt="Foto do usuário">
            <div class="leading-tight">
              <div class="text-sm font-medium line-clamp-1">{{ $user?->name }}</div>
              <div class="text-xs muted line-clamp-1">{{ $user?->email }}</div>
            </div>
          </div>
          @if (Route::has('logout'))
            <form method="POST" action="{{ route('logout') }}" class="mt-3">
              @csrf
              <button type="submit" class="text-sm muted hover:text-rose-400">Sair</button>
            </form>
          @endif
        </div>
      </div>
    </aside>

    <main class="console-main">
      <div class="sticky top-0 z-40 border-b glassbar">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
          <div class="h-14 flex items-center justify-between">
            <div class="flex items-center gap-3 min-w-0">
              <img src="{{ $appLogoLight }}" class="h-7 w-auto rounded-lg bg-white/90 dark:bg-slate-900/90 p-0.5 lg:hidden logo-light-top" alt="Logo Trivento">
              <img src="{{ $appLogoDark }}" class="h-7 w-auto rounded-lg bg-white/90 dark:bg-slate-900/90 p-0.5 lg:hidden logo-dark-top" alt="Logo Trivento">

              <div class="top-title">@yield('page.title','Dashboard do Autor')</div>

              @hasSection('page.breadcrumbs')
                <div class="hidden md:flex items-center gap-2 text-xs muted">
                  @yield('page.breadcrumbs')
                </div>
              @endif
            </div>

            <div class="flex items-center gap-2">
              @yield('page.actions')

              @if (Route::has('autor.submissions.create'))
                <a href="{{ route('autor.submissions.create') }}"
                   class="hidden sm:inline-flex items-center rounded-lg px-3 h-9 text-sm text-white brand">
                  + Nova
                </a>
              @endif

              <div x-data="{open:false}" class="relative" x-cloak>
                <button @click="open=!open" class="inline-flex items-center gap-2 rounded-lg chip px-3 h-9 text-sm" aria-haspopup="menu" :aria-expanded="open">
                  <template x-if="theme==='dark'">
                    <svg width="16" height="16" viewBox="0 0 24 24">
                      <path fill="currentColor" d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79Z"/>
                    </svg>
                  </template>
                  <template x-if="theme==='light'">
                    <svg width="16" height="16" viewBox="0 0 24 24">
                      <path fill="currentColor" d="M12 4a1 1 0 0 1 1 1v2a1 1 0 1 1-2 0V5a1 1 0 0 1 1-1Zm0 9a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm7-3a1 1 0 0 1 1 1 8 8 0 0 1-.5 2.77 1 1 0 1 1-1.88-.68A6 6 0 0 0 18 13a1 1 0 0 1 1-1ZM5 12a1 1 0 0 1 1-1 6 6 0 0 0 1.38-.17 1 1 0 1 1 .49 1.94A8 8 0 0 1 5 13a1 1 0 0 1-1-1Zm2.64 5.36a1 1 0 0 1 1.4 0A6 6 0 0 0 12 18a6 6 0 0 0 2.95-.79 1 1 0 0 1 1 1.74A8 8 0 0 1 4 13a1 1 0 0 1 2 0 6 6 0 0 0 1.64 4.36Z"/>
                    </svg>
                  </template>
                  <template x-if="theme==='auto'">
                    <svg width="16" height="16" viewBox="0 0 24 24">
                      <path fill="currentColor" d="M12 3a1 1 0 0 1 1 1v1.06A7 7 0 0 1 19.94 11H21a1 1 0 0 1 0 2h-1.06A7 7 0 0 1 13 19.94V21a1 1 0 0 1-2 0v-1.06A7 7 0 0 1 4.06 13H3a1 1 0 1 1 0-2h1.06A7 7 0 0 1 11 5.06V4a1 1 0 0 1 1-1Z"/>
                    </svg>
                  </template>
                  <span class="text-sm" x-text="labelTheme()"></span>
                </button>
                <div x-show="open" @click.outside="open=false" class="absolute right-0 mt-2 w-44 rounded-xl panel border p-2 z-50" role="menu">
                  <button @click="setTheme('auto');  open=false" class="w-full text-left rounded-lg px-3 py-2 text-sm hover:bg-white/40 dark:hover:bg-white/5" :class="theme==='auto'  ? 'text-rose-500 dark:text-rose-300 font-medium':''">Automático</button>
                  <button @click="setTheme('light'); open=false" class="w-full text-left rounded-lg px-3 py-2 text-sm hover:bg:white/40 dark:hover:bg-white/5" :class="theme==='light' ? 'text-rose-500 dark:text-rose-300 font-medium':''">Modo claro</button>
                  <button @click="setTheme('dark');  open=false" class="w-full text-left rounded-lg px-3 py-2 text-sm hover:bg-white/40 dark:hover:bg-white/5" :class="theme==='dark'  ? 'text-rose-500 dark:text-rose-300 font-medium':''">Modo escuro</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div id="conteudo" class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 py-4 pb-[calc(env(safe-area-inset-bottom)+88px)]">
        <div class="panel-2 border p-3 sm:p-5 md:p-6">
          @yield('content')
        </div>
      </div>
    </main>
  </div>

  @php
    $tabHomeActive  = request()->routeIs('autor.dashboard');
    $tabSubsActive  = request()->routeIs('autor.submissions.*');
    $tabNotiActive  = request()->routeIs('autor.notifications.*');
    $tabPerfilActive = request()->routeIs('profile.*');
  @endphp

  <nav class="lg:hidden fixed bottom-0 inset-x-0 z-40 bottom-nav" role="navigation" aria-label="Abas inferior">
    <div class="grid grid-cols-4 text-xs">
      <a href="{{ Route::has('autor.dashboard') ? route('autor.dashboard') : '#' }}"
         class="bottom-tab {{ $tabHomeActive ? 'bottom-tab-active' : '' }}"
         aria-current="{{ $tabHomeActive ? 'page' : 'false' }}">
        <div class="bottom-tab-icon-wrap">
          <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M4 10.5 12 4l8 6.5v7.5a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 4 18V10.5Z"
                  stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M10 19.5v-4.5h4v4.5"
                  stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <span>Home</span>
      </a>

      <a href="{{ Route::has('autor.submissions.index') ? route('autor.submissions.index') : '#' }}"
         class="bottom-tab {{ $tabSubsActive ? 'bottom-tab-active' : '' }}"
         aria-current="{{ $tabSubsActive ? 'page' : 'false' }}">
        <div class="bottom-tab-icon-wrap">
          <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M8 4.75h5.5L18 9.25V19a1.25 1.25 0 0 1-1.25 1.25H8A1.25 1.25 0 0 1 6.75 19V6A1.25 1.25 0 0 1 8 4.75Z"
                  stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M13.25 4.75V9.5H18"
                  stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <span>Submissões</span>
      </a>

      <a href="{{ Route::has('autor.notifications.index') ? route('autor.notifications.index') : '#' }}"
         class="relative bottom-tab {{ $tabNotiActive ? 'bottom-tab-active' : '' }}"
         aria-current="{{ $tabNotiActive ? 'page' : 'false' }}">
        <div class="bottom-tab-icon-wrap">
          <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M12 21.25a2.25 2.25 0 0 0 2.24-2h-4.5a2.25 2.25 0 0 0 2.26 2Z"
                  stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M18.25 15.5v-4.2A6.25 6.25 0 0 0 12 5a6.25 6.25 0 0 0-6.25 6.3v4.2L4 17.75h16l-1.75-2.25Z"
                  stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <span>Notificações</span>
        @php $unreadTab = (int) (auth()->user()?->unreadNotifications()->count() ?? 0); @endphp
        @if($unreadTab > 0)
          <span class="absolute top-1.5 right-[22%] inline-flex h-4 min-w-4 px-1 items-center justify-center rounded-full text-[10px] text-white" style="background:var(--brand)">
            {{ $unreadTab }}
          </span>
        @endif
      </a>

      <a href="{{ Route::has('profile.edit') ? route('profile.edit') : '#' }}"
         class="bottom-tab {{ $tabPerfilActive ? 'bottom-tab-active' : '' }}"
         aria-current="{{ $tabPerfilActive ? 'page' : 'false' }}">
        <div class="bottom-tab-icon-wrap">
          <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M12 13a4 4 0 1 0-4-4 4 4 0 0 0 4 4Z"
                  stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M5.5 19.25A6.5 6.5 0 0 1 12 15.5a6.5 6.5 0 0 1 6.5 3.75"
                  stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <span>Perfil</span>
      </a>
    </div>
  </nav>

  @if (Route::has('autor.submissions.create'))
    <a href="{{ route('autor.submissions.create') }}"
       class="lg:hidden fixed bottom-[88px] right-4 z-40 inline-flex items-center justify-center h-12 w-12 rounded-full text-white shadow-xl"
       style="background:var(--brand)" aria-label="Nova submissão">+</a>
  @endif

  <script>
    function shell(){
      return {
        theme: window.__TRV_INITIAL_THEME__ || 'auto',
        mql: null,
        init(){
          this.mql = matchMedia('(prefers-color-scheme: dark)');
          if (this.mql && this.mql.addEventListener) {
            this.mql.addEventListener('change', () => {
              if (this.theme === 'auto') this.apply(this.theme);
            });
          }
          this.apply(this.theme);
        },
        labelTheme(){
          return this.theme === 'auto' ? 'Automático'
               : this.theme === 'dark' ? 'Escuro'
               : 'Claro';
        },
        cycleTheme(){
          this.setTheme(this.theme === 'auto' ? 'light'
                     : this.theme === 'light' ? 'dark'
                     : 'auto');
        },
        setTheme(t){
          this.theme = t;
          localStorage.setItem('trv.theme', t);
          this.apply(t);
        },
        apply(t){
          const prefersDark = this.mql ? this.mql.matches : false;
          const dark = t === 'dark' || (t === 'auto' && prefersDark);
          document.documentElement.classList.toggle('dark', dark);
          const meta = document.querySelector('meta[name="theme-color"]');
          if (meta) meta.setAttribute('content', dark ? '#050608' : '#FFFFFF');
        }
      }
    }
  </script>

  @stack('scripts')
</body>
</html>
