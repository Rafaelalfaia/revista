@extends('console.layout-author')

@section('title','Notificações · Autor')
@section('page.title','Notificações')

@push('head')
<style>
  .notif-shell{display:flex;flex-direction:column;gap:1rem}
  .notif-flash{border-radius:.9rem;border:1px solid var(--line);background:var(--panel);padding:.6rem .8rem;font-size:.8rem}
  .notif-flash-ok{border-color:rgba(16,185,129,.45);background:rgba(16,185,129,.1);color:#047857}
  .notif-flash-warn{border-color:rgba(245,158,11,.45);background:rgba(245,158,11,.1);color:#854d0e}
  .notif-flash-err{border-color:rgba(248,113,113,.6);background:rgba(254,226,226,1);color:#b91c1c}

  .notif-header{display:flex;align-items:center;justify-content:space-between;gap:.75rem;flex-wrap:wrap}
  .notif-header-left{display:flex;flex-direction:column;gap:.25rem}
  .notif-header-title{font-size:.98rem;font-weight:700}
  .notif-header-sub{font-size:.78rem;color:var(--muted)}
  .notif-tabs{display:inline-flex;padding:.18rem;border-radius:999px;background:var(--soft);border:1px solid var(--line);margin-top:.25rem}
  .notif-tab{border-radius:999px;padding:.25rem .9rem;font-size:.78rem;font-weight:500;display:inline-flex;align-items:center;gap:.35rem;border:none;background:transparent;color:var(--muted);text-decoration:none}
  .notif-tab-active{background:var(--panel);color:var(--text);box-shadow:0 1px 2px rgba(15,23,42,.18)}
  .notif-pill{font-size:.72rem;border-radius:999px;padding:.08rem .45rem;background:rgba(15,23,42,.08)}
  .notif-total{font-size:.75rem;color:var(--muted)}

  .btn{border-radius:.9rem;padding:.4rem .9rem;font-size:.78rem;font-weight:600;display:inline-flex;align-items:center;gap:.3rem;border:1px solid transparent;background:var(--panel);color:var(--text)}
  .btn-sm{padding:.35rem .8rem;font-size:.76rem}
  .btn-ghost{border-color:var(--line);background:var(--panel)}
  .btn-ghost:hover{background:rgba(148,163,184,.12)}
  .btn-brand{background:var(--brand);color:#fff;border:none}
  .btn-brand:hover{filter:brightness(1.03)}
  .btn-soft-danger{border-color:rgba(248,113,113,.55);background:rgba(248,113,113,.06);color:#b91c1c}
  .btn-soft-danger:hover{background:rgba(248,113,113,.14)}

  .notif-empty{border-radius:1.2rem;border:1px dashed var(--line);padding:2rem 1.4rem;text-align:center;font-size:.85rem;color:var(--muted);background:var(--panel)}

  .notif-list{border-radius:1.2rem;border:1px solid var(--line);overflow:hidden;background:var(--panel)}
  .notif-card{display:flex;gap:.75rem;padding:.8rem 1rem;border-top:1px solid var(--line)}
  .notif-card:first-child{border-top:none}
  .notif-card-unread{background:radial-gradient(circle at left top,rgba(251,113,133,.12),transparent 60%),var(--panel)}
  .notif-card-read{opacity:.9}
  .notif-strip{width:.26rem;border-radius:999px;background:transparent;flex-shrink:0;align-self:stretch}
  .notif-strip-unread{background:var(--brand)}

  .notif-icon{width:2.1rem;height:2.1rem;border-radius:999px;display:flex;align-items:center;justify-content:center;font-size:1rem;background:rgba(15,23,42,.06);color:var(--muted);flex-shrink:0}
  .notif-card-unread .notif-icon{background:rgba(251,113,133,.15);color:#b91c1c}

  .notif-main{min-width:0;flex:1}
  .notif-title-row{display:flex;align-items:center;gap:.4rem;flex-wrap:wrap}
  .notif-title{font-size:.9rem;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
  .notif-badge{font-size:.7rem;border-radius:999px;padding:.12rem .55rem;background:rgba(15,23,42,.06);color:var(--muted)}
  .notif-badge-unread{background:rgba(251,113,133,.12);color:#b91c1c}

  .notif-body{margin-top:.25rem;font-size:.8rem;color:var(--muted);white-space:pre-line}
  .notif-meta{margin-top:.3rem;font-size:.72rem;color:var(--muted)}

  .notif-actions{display:flex;flex-direction:column;gap:.25rem;align-items:flex-end;flex-shrink:0}
  @media(max-width:640px){
    .notif-card{flex-direction:column}
    .notif-actions{flex-direction:row;justify-content:flex-start;align-items:center}
  }
</style>
@endpush

@php
  $user = auth()->user();
  $unreadCount = (int) ($user?->unreadNotifications()->count() ?? 0);
  $isUnread = request('status') === 'unread';
@endphp

@section('page.actions')
  @if($unreadCount > 0)
    <form action="{{ route('autor.notifications.read_all') }}" method="POST">
      @csrf
      <button class="btn btn-brand btn-sm">
        Marcar todas como lidas ({{ $unreadCount }})
      </button>
    </form>
  @endif
@endsection

@section('content')
  <div class="notif-shell">
    @if(session('ok'))
      <div class="notif-flash notif-flash-ok">{{ session('ok') }}</div>
    @endif
    @if(session('warn'))
      <div class="notif-flash notif-flash-warn">{{ session('warn') }}</div>
    @endif
    @if($errors->any())
      <div class="notif-flash notif-flash-err">
        <ul class="list-disc ml-4">
          @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="notif-header">
      <div class="notif-header-left">
        <div class="notif-header-title">Caixa de notificações</div>
        <div class="notif-header-sub">
          Acompanhe atualizações sobre suas submissões e revisões.
        </div>
        <div class="notif-tabs">
          <a href="{{ route('autor.notifications.index') }}"
             class="notif-tab {{ $isUnread ? '' : 'notif-tab-active' }}">
            <span>Todas</span>
          </a>
          <a href="{{ route('autor.notifications.index', ['status'=>'unread']) }}"
             class="notif-tab {{ $isUnread ? 'notif-tab-active' : '' }}">
            <span>Não lidas</span>
            <span class="notif-pill">{{ $unreadCount }}</span>
          </a>
        </div>
      </div>
      <div class="notif-total">
        {{ $notifications->total() }} resultado(s)
      </div>
    </div>

    @if($notifications->count() === 0)
      <div class="notif-empty">
        Nenhuma notificação {{ $isUnread ? 'não lida' : 'encontrada' }} no momento.
      </div>
    @else
      <div class="notif-list">
        @foreach($notifications as $n)
          @php
            $data = $n->data ?? [];
            $title = $data['title'] ?? class_basename($n->type);
            $message = $data['message'] ?? null;
            if (!$message) {
              $flat = collect($data)->map(fn($v,$k)=> $k.': '.(is_scalar($v)?$v:json_encode($v)))->implode(' • ');
              $message = \Illuminate\Support\Str::limit($flat, 240);
            }
            $is_read = !is_null($n->read_at);
          @endphp

          <div class="notif-card {{ $is_read ? 'notif-card-read' : 'notif-card-unread' }}">
            <div class="notif-strip {{ $is_read ? '' : 'notif-strip-unread' }}"></div>

            <div class="notif-icon">
              @if(!$is_read)
                !
              @else
                ✓
              @endif
            </div>

            <div class="notif-main">
              <div class="notif-title-row">
                <div class="notif-title" title="{{ $title }}">{{ $title }}</div>
                @if(!$is_read)
                  <span class="notif-badge notif-badge-unread">Não lida</span>
                @else
                  <span class="notif-badge">Lida</span>
                @endif
              </div>

              <div class="notif-body">
                {{ $message }}
              </div>

              <div class="notif-meta">
                Recebida em {{ $n->created_at?->timezone(config('app.timezone'))->format('d/m/Y H:i') }}
              </div>
            </div>

            <div class="notif-actions">
              <a href="{{ route('autor.notifications.show', $n) }}" class="btn btn-ghost btn-sm">
                Ver detalhes
              </a>

              @if(!$is_read)
                <form action="{{ route('autor.notifications.read', $n) }}" method="POST">
                  @csrf
                  <button class="btn btn-brand btn-sm">
                    Marcar como lida
                  </button>
                </form>
              @endif

              <form action="{{ route('autor.notifications.destroy', $n) }}" method="POST" onsubmit="return confirm('Remover esta notificação?')">
                @csrf
                @method('DELETE')
                <button class="btn btn-soft-danger btn-sm">
                  Excluir
                </button>
              </form>
            </div>
          </div>
        @endforeach
      </div>

      <div class="mt-4">
        {{ $notifications->withQueryString()->links() }}
      </div>
    @endif
  </div>
@endsection
