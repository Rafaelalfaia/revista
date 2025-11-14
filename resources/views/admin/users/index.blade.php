@extends('console.layout')
@section('title','Pessoas')
@section('page.title','Pessoas')

@push('head')
<style>
  .pill-toggle{border-radius:999px;border:1px solid var(--line);padding:.25rem .9rem;font-size:.8rem;white-space:nowrap;display:inline-flex;align-items:center;gap:.35rem;background:var(--panel-2);cursor:pointer}
  .pill-toggle span{font-size:.75rem;opacity:.75}
  .pill-toggle-active{background:var(--brand);color:#fff;border-color:transparent}
  .pill-toggle-active span{opacity:.9}
  .user-shell{display:flex;flex-direction:column;gap:1.25rem}
  .role-section{border-radius:1.1rem;border:1px solid var(--line);background:var(--panel)}
  .role-header{display:flex;align-items:center;justify-content:space-between;padding:.85rem 1rem;border-bottom:1px solid var(--line);gap:.75rem}
  .role-title{font-size:.9rem;font-weight:600;display:flex;align-items:center;gap:.4rem}
  .role-badge{border-radius:999px;padding:.1rem .6rem;font-size:.7rem;background:var(--soft);border:1px solid var(--line)}
  .role-count{font-size:.75rem;color:var(--muted)}
  .role-body{padding:.75rem;display:grid;grid-template-columns:1fr;gap:.6rem}
  @media(min-width:768px){.role-body{grid-template-columns:repeat(2,minmax(0,1fr))}}
  @media(min-width:1280px){.role-body{grid-template-columns:repeat(3,minmax(0,1fr))}}
  .user-card{border-radius:.9rem;border:1px solid var(--line);background:var(--panel-2);padding:.75rem .8rem;display:flex;align-items:center;justify-content:space-between;gap:.75rem}
  .user-main{display:flex;align-items:center;gap:.65rem;min-width:0}
  .user-avatar{width:2.4rem;height:2.4rem;border-radius:999px;object-fit:cover;border:1px solid var(--line);flex-shrink:0;background:var(--soft)}
  .user-text{min-width:0}
  .user-name{font-size:.9rem;font-weight:600;white-space:nowrap;text-overflow:ellipsis;overflow:hidden}
  .user-email{font-size:.75rem;color:var(--muted);white-space:nowrap;text-overflow:ellipsis;overflow:hidden}
  .user-meta{font-size:.7rem;color:var(--muted);margin-top:.15rem}
  .user-actions{display:flex;flex-direction:column;align-items:flex-end;gap:.35rem;flex-shrink:0}
  .chip-role{border-radius:999px;padding:.15rem .55rem;font-size:.7rem;border:1px solid var(--line);background:var(--soft);white-space:nowrap}
  .btn-ghost{border-radius:.6rem;border:1px solid var(--line);padding:.25rem .75rem;font-size:.75rem;display:inline-flex;align-items:center;gap:.25rem;background:var(--panel)}
  .btn-danger{color:#fb7185}
  .flash{border-radius:.85rem;border:1px solid var(--line);background:var(--panel);padding:.55rem .75rem;font-size:.8rem}
  .flash-err{color:#fb7185}
  [x-cloak]{display:none}
</style>
@endpush

@section('content')
  @if (session('ok'))
    <div class="mb-3 flash">{{ session('ok') }}</div>
  @endif
  @if (session('err'))
    <div class="mb-3 flash flash-err">{{ session('err') }}</div>
  @endif

  <div class="flex flex-col md:flex-row md:items-center gap-3 mb-5">
    <form method="GET" class="flex-1">
      <input type="search" name="q" value="{{ $q }}" placeholder="Buscar por nome ou e-mail"
             class="w-full rounded-xl border px-3 h-10 panel text-sm" style="border-color:var(--line)">
    </form>

    @can('users.manage')
      <a href="{{ route('admin.users.create') }}"
         class="brand text-white rounded-xl px-4 h-10 inline-flex items-center justify-center text-sm font-semibold">
        + Nova pessoa
      </a>
    @endcan
  </div>

  @php
    $grouped = $users->groupBy(function($u){
        return $u->roles->pluck('name')->first() ?? 'Sem papel';
    });
    $roleMap = [
      'Admin'       => 'Admins',
      'Coordenador' => 'Coordenadores',
      'Revisor'     => 'Revisores',
      'Autor'       => 'Autores',
    ];
  @endphp

  <div x-data="{ role: 'all' }" class="space-y-4">
    <div class="flex gap-2 overflow-x-auto pb-1">
      <button type="button"
              @click="role = 'all'"
              :class="role === 'all' ? 'pill-toggle pill-toggle-active' : 'pill-toggle'">
        <span>Todos</span>
        <span>{{ $users->total() }}</span>
      </button>
      @foreach($grouped as $roleName => $list)
        @php
          $key = \Illuminate\Support\Str::slug($roleName ?: 'sem-papel');
          $label = $roleMap[$roleName] ?? $roleName;
        @endphp
        <button type="button"
                @click="role = '{{ $key }}'"
                :class="role === '{{ $key }}' ? 'pill-toggle pill-toggle-active' : 'pill-toggle'">
          <span>{{ $label }}</span>
          <span>{{ $list->count() }}</span>
        </button>
      @endforeach
    </div>

    <div class="user-shell">
      @foreach($grouped as $roleName => $list)
        @php
          $key = \Illuminate\Support\Str::slug($roleName ?: 'sem-papel');
          $label = $roleMap[$roleName] ?? ($roleName ?: 'Sem papel');
        @endphp
        <section class="role-section" x-show="role === 'all' || role === '{{ $key }}'" x-cloak>
          <header class="role-header">
            <div class="role-title">
              <span class="role-badge">{{ $label }}</span>
              <span class="role-count">{{ $list->count() }} {{ $list->count() === 1 ? 'pessoa' : 'pessoas' }} nesta página</span>
            </div>
          </header>

          <div class="role-body">
            @foreach($list as $u)
              @php
                $candidate = public_path("images/avatars/{$u->id}.png");
                $avatar = is_file($candidate)
                  ? asset("images/avatars/{$u->id}.png").'?v='.substr(md5_file($candidate),0,8)
                  : asset('images/avatar.png');
                $roleSingle = $u->roles->pluck('name')->first() ?? 'Sem papel';
              @endphp
              <div class="user-card">
                <div class="user-main">
                  <img src="{{ $avatar }}" alt="Foto de {{ $u->name }}" class="user-avatar">
                  <div class="user-text">
                    <div class="user-name" title="{{ $u->name }}">{{ $u->name }}</div>
                    <div class="user-email" title="{{ $u->email }}">{{ $u->email }}</div>
                    @if($u->created_at)
                      <div class="user-meta">
                        Conta desde {{ $u->created_at->format('d/m/Y') }}
                      </div>
                    @endif
                  </div>
                </div>
                <div class="user-actions">
                  <span class="chip-role">{{ $roleSingle }}</span>
                  <div class="flex gap-2">
                    @can('users.manage')
                      <a href="{{ route('admin.users.edit',$u) }}" class="btn-ghost text-xs">
                        Editar
                      </a>
                      <form action="{{ route('admin.users.destroy',$u) }}" method="POST"
                            onsubmit="return confirm('Excluir esta pessoa?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-ghost btn-danger text-xs">
                          Excluir
                        </button>
                      </form>
                    @endcan
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        </section>
      @endforeach
    </div>
  </div>

  <div class="mt-5">
    {{ $users->links() }}
  </div>
@endsection
