<!DOCTYPE html>
<html lang="pt-BR" class="antialiased">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>@yield('title','Console · Trivento')</title>

  <script>
    (() => {
      const KEY = 'trv.theme';
      const saved = localStorage.getItem(KEY) || 'auto';
      const prefersDark = matchMedia('(prefers-color-scheme: dark)').matches;
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
      --brand:#E11D48; --brand-700:#BE123C;
      --chip:rgba(225,29,72,.08);
      --glass:rgba(255,255,255,.78); --glass-line:rgba(15,23,42,.10);
      color-scheme: light dark;
    }
    .dark{
      --bg:#050608; --text:#E5E7EB; --muted:#94A3B8;
      --panel:#0F1412; --panel-2:#0B0F0D; --line:rgba(255,255,255,.08);
      --brand:#E11D48; --brand-700:#BE123C;
      --chip:rgba(225,29,72,.12);
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
      border-radius:1.2rem;
      overflow:visible;
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
    [x-cloak]{display:none}

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

    .console-sidebar{
      position:fixed;
      top:12px;
      bottom:12px;
      left:12px;
      width:280px;
      z-index:50;
      display:block;
      transform:translateX(-110%);
      transition:transform .22s ease-out;
    }
    .console-sidebar-open{
      transform:translateX(0);
    }
    @media(min-width:1024px){
      .console-sidebar{
        transform:none;
      }
      .console-main{
        margin-left:308px;
      }
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
  </style>

  <meta name="color-scheme" content="light dark">
  <meta name="theme-color" content="#050608">
  @vite(['resources/css/app.css','resources/js/app.js'])
  @stack('head')
</head>
<body x-data="shell()" x-init="init()" :class="menuOpen ? 'console-root overflow-hidden' : 'console-root'">

  <a href="#conteudo"
     class="sr-only focus:not-sr-only focus:fixed focus:left-3 focus:top-3 focus:z-[60] rounded-md px-3 py-2 text-white"
     style="background:var(--brand)">Ir para o conteúdo</a>

  @php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Route as R;

    $u = auth()->user();

    $cfg = config('console.menu', config('auth.menu', []));
    $roleKey = collect(array_keys($cfg))->first(function($r) use ($u){
        try { return $u && method_exists($u,'hasRole') ? $u->hasRole($r) : false; }
        catch (\Throwable $e) { return false; }
    }) ?? (array_key_exists('Admin',$cfg) ? 'Admin' : (array_key_first($cfg)));

    $menu = $roleKey && isset($cfg[$roleKey]) ? $cfg[$roleKey] : [];
    if (empty($menu) && isset($cfg['Admin'])) $menu = $cfg['Admin'];

    $isAdmin      = ($roleKey === 'Admin')      || ($u && method_exists($u,'hasRole') && $u->hasRole('Admin'));
    $isRevisor    = ($roleKey === 'Revisor')    || ($u && method_exists($u,'hasRole') && $u->hasRole('Revisor'));
    $isCoordenador= ($roleKey === 'Coordenador')|| ($u && method_exists($u,'hasRole') && $u->hasRole('Coordenador'));

    $actionsCfg = config('console.actions', config('auth.actions', []));
    $act = collect($actionsCfg)->first(fn($v,$pattern) => request()->routeIs($pattern));
    $actRouteExists = $act && isset($act['route']) && R::has($act['route']);
    $canAct = $act && ($isAdmin || empty($act['can']) || ($u && $u->can($act['can'])));

    $isRouteActive = function(string $routeName) {
        if (Str::contains($routeName, '*')) {
            return request()->routeIs($routeName);
        }
        return request()->routeIs($routeName) || request()->routeIs($routeName.'.*');
    };

    $appLogoLight = asset('images/logo.png');
    $appLogoDark  = asset('images/logo1.png');
  @endphp

  <div class="console-body">

    <aside id="console-sidebar" class="console-sidebar" :class="menuOpen ? 'console-sidebar-open' : ''" role="navigation" aria-label="Menu lateral">
      <div class="console-sidebar-inner panel border px-4 py-4 flex flex-col">
        <div class="console-logo-wrap">
          <img src="{{ $appLogoLight }}" alt="Logo Trivento" class="console-logo-img logo-light">
          <img src="{{ $appLogoDark }}" alt="Logo Trivento" class="console-logo-img logo-dark">
        </div>

        @php
          $perfilExists = \Illuminate\Support\Facades\Route::has('profile.edit');
          $perfilActive = $perfilExists && $isRouteActive('profile.edit');
          $jaTemPerfil  = collect($menu)->contains(fn($i) => ($i['route'] ?? null) === 'profile.edit');
        @endphp

        <nav class="mt-4 space-y-1 text-sm flex-1">
          @foreach ($menu as $it)
            @php
              $rt    = $it['route'] ?? '#';
              $can   = $it['can']   ?? null;
              $show  = !$can || ($u && $u->can($can)) || $isAdmin;
              $routeExists = $rt !== '#' && R::has($rt);
              $href   = $routeExists ? route($rt) : '#';
              $active = $routeExists && $isRouteActive($rt);
            @endphp
            @if ($show)
              <x-console.navlink href="{{ $href }}" :active="$active">
                {{ $it['label'] }}
              </x-console.navlink>
            @endif
          @endforeach

          @if ($perfilExists && !$jaTemPerfil)
            <x-console.navlink href="{{ route('profile.edit') }}" :active="$perfilActive">
              Perfil
            </x-console.navlink>
          @endif
        </nav>

        <div class="mt-6 border-t pt-4" style="border-color:var(--line);">
          <div class="text-xs muted">Sessão</div>
          @php
            $avatar = ($u && !empty($u->avatar_url)) ? $u->avatar_url : asset('images/avatar.png');
          @endphp
          <div class="mt-2 flex items-center gap-3">
            <img src="{{ $avatar }}" class="h-9 w-9 rounded-full object-cover" alt="Foto de {{ $u?->name ?? 'usuário' }}">
            <div class="leading-tight">
              <div class="text-sm font-medium line-clamp-1">{{ $u?->name }}</div>
              <div class="text-xs muted line-clamp-1">{{ $u?->email }}</div>
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

    <div x-show="menuOpen" x-transition.opacity class="fixed inset-0 z-40 bg-black/45 lg:hidden" @click="closeMenu()" aria-hidden="true"></div>

    <main class="console-main">
      <div class="sticky top-0 z-40 border-b glassbar">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
          <div class="h-14 flex items-center justify-between">
            <div class="flex items-center gap-3 min-w-0">
              @if($isAdmin)
                <button class="lg:hidden inline-flex h-9 w-9 items-center justify-center rounded-lg chip"
                        @click="openMenu($el)" aria-controls="console-sidebar" aria-label="Abrir menu">
                  <svg width="18" height="18" viewBox="0 0 24 24">
                    <path d="M4 7h16a1 1 0 0 0 0-2H4a1 1 0 0 0 0 2Zm0 6h12a1 1 0 0 0 0-2H4a1 1 0 0 0 0 2Zm0 6h8a1 1 0 0 0 0-2H4a1 1 0 0 0 0 2Z" fill="currentColor"/>
                  </svg>
                </button>
              @endif

              <img src="{{ $appLogoLight }}" class="h-7 w-auto rounded-lg bg-white/90 dark:bg-slate-900/90 p-0.5 lg:hidden logo-light-top" alt="Logo Trivento">
              <img src="{{ $appLogoDark }}" class="h-7 w-auto rounded-lg bg-white/90 dark:bg-slate-900/90 p-0.5 lg:hidden logo-dark-top" alt="Logo Trivento">

              <div class="top-title">@yield('page.title','Dashboard')</div>
              @hasSection('page.breadcrumbs')
                <div class="hidden md:flex items-center gap-2 text-xs muted">
                  @yield('page.breadcrumbs')
                </div>
              @endif
            </div>

            <div class="flex items-center gap-2">
              @yield('page.actions')

              @if ($canAct && $actRouteExists)
                <a href="{{ route($act['route']) }}"
                   class="hidden sm:inline-flex items-center rounded-lg px-3 h-9 text-sm text-white brand">
                   {{ $act['label'] }}
                </a>
              @endif

              <div x-data="{open:false}" class="relative" x-cloak>
                <button id="theme-btn" @click="open=!open"
                        class="inline-flex items-center gap-2 rounded-lg chip px-3 h-9 text-sm"
                        :aria-expanded="open.toString()" aria-haspopup="menu" aria-controls="theme-menu">
                  <template x-if="theme==='dark'">
                    <svg width="16" height="16" viewBox="0 0 24 24">
                      <path fill="currentColor" d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79Z"/>
                    </svg>
                  </template>
                  <template x-if="theme==='light'">
                    <svg width="16" height="16" viewBox="0 0 24 24">
                      <path fill="currentColor" d="M12 4a1 1 0 0 1 1 1v2a1 1 0 1 1-2 0V5a1 1 0 0 1 1-1Zm0 14a1 1 0 0 1 1 1v0a1 1 0 1 1-2 0v0a1 1 0 0 1 1-1Zm8-7a1 1 0 0 1 1 1v0a1 1 0 1 1-2 0v0a1 1 0 0 1 1-1ZM5 12a1 1 0 0 1 1-1h0a1 1 0 0 1 0 2h0A1 1 0 0 1 5 12Zm11.66-5.66a1 1 0 0 1 1.41 0v0a1 1 0 1 1-1.41 1.41v0a1 1 0 0 1 0-1.41ZM6.93 17.07a1 1 0 0 1 1.41 0v0a1 1 0 1 1-1.41 1.41v0a1 1 0 0 1 0-1.41Zm0-11.14a1 1 0 0 1 0 1.41v0A1 1 0 1 1 5.52 5.93v0a1 1 0 0 1 1.41 0Zm11.14 11.14a1 1 0 0 1 0 1.41v0a1 1 0 1 1-1.41-1.41v0a1 1 0 0 1 1.41 0ZM12 16a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z"/>
                    </svg>
                  </template>
                  <template x-if="theme==='auto'">
                    <svg width="16" height="16" viewBox="0 0 24 24">
                      <path fill="currentColor" d="M12 3a1 1 0 0 1 1 1v1.06A7 7 0 0 1 19.94 11H21a1 1 0 0 1 0 2h-1.06A7 7 0 0 1 13 19.94V21a1 1 0 0 1-2 0v-1.06A7 7 0 0 1 4.06 13H3a1 1 0 1 1 0-2h1.06A7 7 0 0 1 11 5.06V4a1 1 0 0 1 1-1Z"/>
                    </svg>
                  </template>
                  <span class="text-sm" x-text="labelTheme()"></span>
                </button>

                <div x-show="open" @click.outside="open=false" @keydown.escape.stop="open=false"
                     id="theme-menu" role="menu" aria-labelledby="theme-btn"
                     class="absolute right-0 mt-2 w-44 rounded-xl panel border p-2 z-50">
                  <button role="menuitemradio" :aria-checked="theme==='auto'" @click="setTheme('auto'); open=false"
                          class="w-full text-left rounded-lg px-3 py-2 text-sm hover:bg-white/40 dark:hover:bg-white/5"
                          :class="theme==='auto' ? 'text-rose-500 dark:text-rose-300 font-medium' : ''">
                    Automático
                  </button>
                  <button role="menuitemradio" :aria-checked="theme==='light'" @click="setTheme('light'); open=false"
                          class="w-full text-left rounded-lg px-3 py-2 text-sm hover:bg-white/40 dark:hover:bg-white/5"
                          :class="theme==='light' ? 'text-rose-500 dark:text-rose-300 font-medium' : ''">
                    Modo claro
                  </button>
                  <button role="menuitemradio" :aria-checked="theme==='dark'" @click="setTheme('dark'); open=false"
                          class="w-full text-left rounded-lg px-3 py-2 text-sm hover:bg-white/40 dark:hover:bg-white/5"
                          :class="theme==='dark' ? 'text-rose-500 dark:text-rose-300 font-medium' : ''">
                    Modo escuro
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="border-b panel-2">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 py-2">
          @stack('context')
        </div>
      </div>

      <div id="conteudo" class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 py-4 pb-[calc(env(safe-area-inset-bottom)+88px)]">
        <div class="panel-2 border p-4 sm:p-6">
          @yield('content')
        </div>

        @if (request()->routeIs('*dashboard*') || request()->routeIs('dashboard'))
          <div class="mt-4">@includeIf('console.partials._banner_principal')</div>
        @endif
      </div>
    </main>
  </div>

  @php
    $bottomNav = [];

    if ($isRevisor) {
        $bottomNav = [
            ['label' => 'Dashboard',       'route' => 'revisor.dashboard',         'icon' => 'home'],
            ['label' => 'Minhas Revisões', 'route' => 'revisor.reviews.index',     'icon' => 'reviews'],
            ['label' => 'Perfil',          'route' => 'profile.edit',              'icon' => 'profile'],
        ];
    } elseif ($isCoordenador) {

        // Pega as rotas do próprio menu lateral do Coordenador
        $menuCol = collect($menu)->keyBy('label');

        $bottomNav = [
            [
                'label' => 'Dashboard',
                'route' => $menuCol['Dashboard']['route'] ?? 'coordenador.dashboard',
                'icon'  => 'home',
            ],
            [
                'label' => 'Submissões',
                'route' => $menuCol['Submissões']['route'] ?? 'coordenador.submissions.index',
                'icon'  => 'submissions',
            ],
            [
                'label' => 'Revisores',
                'route' => $menuCol['Revisores']['route'] ?? 'coordenador.revisores.index',
                'icon'  => 'reviewers',
            ],
            [
                'label' => 'Relatórios',
                'route' => $menuCol['Relatórios']['route'] ?? 'coordenador.relatorios.index',
                'icon'  => 'reports',
            ],
            [
                'label' => 'Perfil',
                'route' => 'profile.edit',
                'icon'  => 'profile',
            ],
        ];

    } elseif ($isAdmin) {
        $bottomNav = [
            ['label' => 'Dashboard',   'route' => 'dashboard',               'icon' => 'home'],
            ['label' => 'Submissões',  'route' => 'admin.submissions.index', 'icon' => 'submissions'],
            ['label' => 'Usuários',    'route' => 'admin.users.index',       'icon' => 'users'],
            ['label' => 'Perfil',      'route' => 'profile.edit',            'icon' => 'profile'],
        ];
    }

    $colsClass = 'grid-cols-3';
    if (count($bottomNav) === 4) $colsClass = 'grid-cols-4';
    if (count($bottomNav) === 5) $colsClass = 'grid-cols-5';
@endphp


  @if(count($bottomNav))
  <nav class="lg:hidden fixed bottom-0 inset-x-0 z-40 bottom-nav">
    <div class="grid {{ $colsClass }} text-xs">
      @foreach($bottomNav as $item)
        @php
          $routeExists = \Illuminate\Support\Facades\Route::has($item['route']);
          $href   = $routeExists ? route($item['route']) : '#';
          $active = $routeExists && $isRouteActive($item['route']);
        @endphp
        <a href="{{ $href }}"
           class="bottom-tab {{ $active ? 'bottom-tab-active' : '' }}"
           aria-current="{{ $active ? 'page' : 'false' }}">
          <div class="bottom-tab-icon-wrap">
            @switch($item['icon'])
              @case('home')
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                  <path d="M4.5 11.5 12 4l7.5 7.5V19a2 2 0 0 1-2 2h-11a2 2 0 0 1-2-2v-7.5Z"
                        stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M10 21v-5h4v5"
                        stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                @break

              @case('submissions')
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                  <path d="M8 4.75h5.5L18 9.25V19a1.25 1.25 0 0 1-1.25 1.25H8A1.25 1.25 0 0 1 6.75 19V6A1.25 1.25 0 0 1 8 4.75Z"
                        stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M13.25 4.75V9.5H18"
                        stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                @break

              @case('users')
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                  <path d="M8.5 11a3 3 0 1 0-3-3 3 3 0 0 0 3 3Z"
                        stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M4 18a4.5 4.5 0 0 1 9 0"
                        stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M17 11a2.5 2.5 0 1 0-2.4-3.2"
                        stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M14.5 18a4 4 0 0 1 6.5-3.1"
                        stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                @break

              @case('reviews')
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                  <path d="M7 4.75h7.5L19 9.25V19a1.25 1.25 0 0 1-1.25 1.25H7A1.25 1.25 0 0 1 5.75 19V6A1.25 1.25 0 0 1 7 4.75Z"
                        stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M13.5 4.75V9.5H19"
                        stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M8.5 14.5h3"
                        stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M8.5 11.5h6"
                        stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                @break

              @case('reviewers')
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                  <path d="M10 11a3 3 0 1 0-3-3 3 3 0 0 0 3 3Z"
                        stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M5 19a5 5 0 0 1 10 0"
                        stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M16.5 11.5 19 14"
                        stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M19.5 10a2 2 0 1 0-2 2 2 2 0 0 0 2-2Z"
                        stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                @break

              @case('reports')
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                  <path d="M5 19.25V11.5"
                        stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M10 19.25V8.5"
                        stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M15 19.25V6.5"
                        stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M20 19.25V10.5"
                        stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                @break

              @case('profile')
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                  <path d="M12 13a4 4 0 1 0-4-4 4 4 0 0 0 4 4Z"
                        stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M5.5 19.25A6.5 6.5 0 0 1 12 15.5a6.5 6.5 0 0 1 6.5 3.75"
                        stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                @break
            @endswitch
          </div>
          <span>{{ $item['label'] }}</span>
        </a>
      @endforeach
    </div>
  </nav>
  @endif

  <script>
    function shell(){
      return {
        theme: window.__TRV_INITIAL_THEME__ || 'auto',
        mql: null,
        menuOpen: false,
        lastBtn: null,
        init(){
          this.mql = matchMedia('(prefers-color-scheme: dark)');
          if (this.mql && this.mql.addEventListener) {
            this.mql.addEventListener('change', () => {
              if (this.theme === 'auto') this.apply(this.theme);
            });
          }
          this.apply(this.theme);
          window.addEventListener('keydown', e => {
            if (e.key === 'Escape' && this.menuOpen) this.closeMenu();
          });
        },
        openMenu(btn){
          this.lastBtn = btn;
          this.menuOpen = true;
        },
        closeMenu(){
          this.menuOpen = false;
          if (this.lastBtn && this.lastBtn.focus) this.lastBtn.focus();
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
