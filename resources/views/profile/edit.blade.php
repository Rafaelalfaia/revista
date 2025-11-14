@php
  $user  = $user ?? auth()->user();
  $isAutor = method_exists($user,'hasRole') && $user->hasRole('Autor');
  $layout  = $isAutor
    ? (view()->exists('console.layout-author') ? 'console.layout-author' : 'layout-author')
    : (view()->exists('console.layout')        ? 'console.layout'        : 'layout');
  $roles = collect();
  if (method_exists($user,'getRoleNames')) { try { $roles = $user->getRoleNames(); } catch (\Throwable $e) {} }
  $fallback  = asset('images/avatar.png');
  $candidate = public_path("images/avatars/{$user->id}.png");
  $avatarUrl = is_file($candidate) ? asset("images/avatars/{$user->id}.png").'?v='.substr(md5_file($candidate),0,8) : $fallback;
@endphp

@extends($layout)

@section('title','Meu Perfil')
@section('page.title','Meu Perfil')

@section('content')
  @if(session('ok'))
    <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-emerald-800">{{ session('ok') }}</div>
  @endif
  @if ($errors->any())
    <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-rose-800">
      <ul class="list-disc list-inside">
        @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
      </ul>
    </div>
  @endif

  <div x-data="profileTabs()" x-init="init()" class="relative grid gap-4">
    <div class="sticky -top-[1px] z-30 rounded-xl border glassbar px-3 py-2">
      <div class="flex items-center justify-between gap-2">
        <div class="flex items-center gap-2">
          <a href="{{ url()->previous() ?: route('dashboard') }}"
             class="inline-flex h-9 w-9 items-center justify-center rounded-lg chip" aria-label="Voltar">
            <svg width="20" height="20" fill="none" class="muted">
              <path d="M12 6l-6 6 6 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </a>
          <div class="font-semibold">Meu Perfil</div>
        </div>
        @if($roles->count())
          <div class="hidden sm:flex flex-wrap gap-1">
            @foreach($roles as $r)
              <span class="px-2 py-0.5 text-xs rounded-md border chip">{{ $r }}</span>
            @endforeach
          </div>
        @endif
      </div>
    </div>

    <div class="rounded-2xl border panel overflow-visible">
      <div class="h-20 w-full rounded-t-2xl" style="background:linear-gradient(135deg,var(--brand,#E11D48),#fb7185)"></div>
      <div class="-mt-10 px-4 pb-4 sm:px-6">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
          <div class="flex items-end gap-3">
            <form action="{{ route('profile.avatar') }}" method="post" enctype="multipart/form-data"
                  x-data="{preview:'{{ $avatarUrl }}'}"
                  @submit.prevent="$refs.file.files.length && $el.submit()"
                  class="relative">
              @csrf
              <img :src="preview" src="{{ $avatarUrl }}"
                   class="h-20 w-20 sm:h-24 sm:w-24 rounded-2xl object-cover border shadow-md"
                   alt="Avatar">
              <input x-ref="file" name="avatar" type="file" accept="image/*" class="sr-only"
                     @change="if($event.target.files[0]){ preview = URL.createObjectURL($event.target.files[0]); $nextTick(()=>{$el.submit()}); }">
              <button type="button" @click="$refs.file.click()"
                      class="absolute -bottom-2 -right-2 sm:bottom-1 sm:right-1 inline-flex h-9 w-9 items-center justify-center rounded-xl brand shadow-md"
                      title="Trocar foto" aria-label="Trocar foto">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M4 7h3l2-2h6l2 2h3v12H4V7zm8 11a5 5 0 100-10 5 5 0 000 10z"/>
                </svg>
              </button>
            </form>

            <div class="pb-1 min-w-0">
              <div class="font-semibold truncate text-base sm:text-lg">{{ $user->name }}</div>
              <div class="text-xs sm:text-sm muted truncate">{{ $user->email }}</div>
              @if($roles->count())
                <div class="mt-1 flex sm:hidden flex-wrap gap-1">
                  @foreach($roles as $r)
                    <span class="px-2 py-0.5 text-[10px] rounded-md border chip">{{ $r }}</span>
                  @endforeach
                </div>
              @endif
            </div>
          </div>

          <div class="w-full sm:w-auto">
            <div class="grid grid-cols-2 gap-2 sm:flex sm:items-center sm:gap-1 sm:rounded-xl sm:border sm:px-1 sm:py-1 sm:bg-[var(--panel,#fff)]">
              <button type="button" class="seg seg-full" :class="tab==='dados' ? 'seg-active' : ''" @click="tab='dados'">Dados</button>
              <button type="button" class="seg seg-full" :class="tab==='senha' ? 'seg-active' : ''" @click="tab='senha'">Senha</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="grid gap-4 lg:grid-cols-3">
      <div class="lg:col-span-2 grid gap-4">
        <form x-show="tab==='dados'" x-transition
              action="{{ route('profile.update') }}" method="post"
              class="card" autocomplete="on">
          @csrf @method('PATCH')
          <div class="card-header">Informações do Perfil</div>
          <div class="card-body grid gap-4 sm:grid-cols-2">
            <label class="field">
              <span>Nome completo</span>
              <input name="name" value="{{ old('name',$user->name) }}" class="input" required autocomplete="name">
            </label>
            <label class="field">
              <span>E-mail</span>
              <input type="email" name="email" value="{{ old('email',$user->email) }}" class="input" required autocomplete="email">
            </label>
            <label class="field sm:col-span-2"
                   x-data="cpfMask('{{ old('cpf', $user->cpf) }}')"
                   x-init="init($el.closest('form'))">
              <span>CPF (opcional)</span>
              <input x-ref="cpf"
                     name="cpf"
                     x-model="masked"
                     @input="format()"
                     @blur="format(true)"
                     class="input"
                     placeholder="000.000.000-00"
                     inputmode="numeric"
                     autocomplete="off"
                     maxlength="14">
            </label>
          </div>
          <div class="card-foot">
            <button class="btn btn-brand">Salvar alterações</button>
          </div>
        </form>

        <form x-show="tab==='senha'" x-transition
              action="{{ route('profile.password') }}" method="post"
              class="card" autocomplete="off">
          @csrf @method('PATCH')
          <div class="card-header">Trocar senha</div>
          <div class="card-body grid gap-4 sm:grid-cols-2">
            <label class="field">
              <span>Senha atual</span>
              <input type="password" name="current_password" class="input" required autocomplete="current-password">
            </label>
            <div></div>
            <label class="field">
              <span>Nova senha</span>
              <input type="password" name="password" class="input" required autocomplete="new-password" minlength="8">
            </label>
            <label class="field">
              <span>Confirmar nova senha</span>
              <input type="password" name="password_confirmation" class="input" required autocomplete="new-password" minlength="8">
            </label>
          </div>
          <div class="card-foot">
            <button class="btn">Atualizar senha</button>
          </div>
        </form>
      </div>

      <div class="grid gap-4">
        <form action="{{ route('profile.avatar') }}" method="post" enctype="multipart/form-data"
              x-data="avatarDrop('{{ $avatarUrl }}')"
              @submit.prevent="$refs.file.files.length && $el.submit()"
              class="card">
          @csrf
          <div class="card-header">Foto de perfil</div>
          <div class="card-body">
            <div class="grid gap-3 sm:grid-cols-[auto,1fr,auto] sm:items-center">
              <img :src="preview" class="h-20 w-20 rounded-2xl object-cover border shadow-sm justify-self-center sm:justify-self-start" alt="Seu avatar">
              <div class="grid gap-2">
                <input type="file" x-ref="file" name="avatar" accept="image/*" class="hidden"
                       @change="if($event.target.files[0]){ preview = URL.createObjectURL($event.target.files[0]); $nextTick(()=>{$el.submit()}); }">
                <div class="grid grid-cols-2 gap-2 sm:flex sm:gap-2">
                  <button type="button" class="btn w-full sm:w-auto" @click="$refs.file.click()">Selecionar</button>
                  <button type="submit" class="btn btn-brand w-full sm:w-auto shrink-0">Enviar</button>
                </div>
                <p class="text-xs muted">JPG/PNG/WEBP até 2MB. Salvo como <code>images/avatars/{{ $user->id }}.png</code>.</p>
              </div>
            </div>
            <div class="mt-3 rounded-xl border-dashed border p-3 text-center"
                 @dragover.prevent
                 @drop.prevent="handleDrop($event)">
              <div class="text-sm">Arraste uma imagem aqui para atualizar</div>
            </div>
          </div>
        </form>

        <div class="card">
          <div class="card-header">Dicas de segurança</div>
          <div class="card-body text-sm grid gap-2">
            <div>• Use uma senha com pelo menos 8 caracteres, letras e números.</div>
            <div>• Evite reutilizar senhas de outros serviços.</div>
            <div>• Mantenha seu e-mail atualizado para recuperação de acesso.</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <style>
    .card{border:1px solid var(--line,#e5e7eb);background:var(--panel,#fff);border-radius:1rem;overflow:visible}
    .card-header{padding:.9rem 1rem;border-bottom:1px solid var(--line,#e5e7eb);font-weight:600}
    .card-body{padding:1rem}
    .card-foot{padding:.75rem 1rem;border-top:1px solid var(--line,#e5e7eb);display:flex;justify-content:flex-end;gap:.5rem}
    .field{display:grid;gap:.35rem}
    .field>span{font-size:.85rem;color:var(--muted,#6b7280)}
    .input{border:1px solid var(--line,#e5e7eb);border-radius:.9rem;padding:.65rem .85rem;background:var(--panel,#fff);width:100%}
    .btn{border:1px solid var(--line,#e5e7eb);border-radius:.9rem;padding:.55rem .9rem;font-weight:600;background:var(--panel,#fff)}
    .btn-brand{background:var(--brand,#E11D48);color:#fff;border-color:transparent}
    .seg{border-radius:.7rem;padding:.5rem .8rem;font-weight:700;border:1px solid var(--line,#e5e7eb);background:var(--panel,#fff)}
    .seg-active{background:var(--brand,#E11D48);color:#fff;border-color:transparent}
    .seg-full{width:100%;text-align:center}
  </style>

  <script>
    (function(){
      const mq = matchMedia('(max-width: 1023.98px)');
      const ensureScroll = () => {
        const s = window.Alpine?.store('console');
        if (!mq.matches) {
          try { s && s.setMenu(false); } catch(e){}
          document.body.classList.remove('overflow-hidden');
          document.body.style.overflowY = '';
        }
      };
      if (document.readyState !== 'loading') ensureScroll();
      else document.addEventListener('DOMContentLoaded', ensureScroll);
      window.addEventListener('pageshow', ensureScroll);
      mq.addEventListener?.('change', ensureScroll);
    })();

    function profileTabs(){
      return {
        tab: 'dados',
        init(){},
      }
    }
    function avatarDrop(initial){
      return {
        preview: initial,
        handleDrop(e){
          const file = e.dataTransfer?.files?.[0];
          if(!file || !file.type.startsWith('image/')) return;
          this.preview = URL.createObjectURL(file);
          const input = this.$refs.file;
          const dt = new DataTransfer();
          dt.items.add(file);
          input.files = dt.files;
          this.$nextTick(()=> this.$el.submit());
        }
      }
    }
    function cpfMask(initialDigits){
      const onlyDigits = (v) => (v || '').replace(/\D+/g, '').slice(0, 11);
      const applyMask = (d) => {
        if (!d) return '';
        if (d.length <= 3)  return d;
        if (d.length <= 6)  return d.slice(0,3)+'.'+d.slice(3);
        if (d.length <= 9)  return d.slice(0,3)+'.'+d.slice(3,6)+'.'+d.slice(6);
        return d.slice(0,3)+'.'+d.slice(3,6)+'.'+d.slice(6,9)+'-'+d.slice(9,11);
      };
      return {
        digits: onlyDigits(initialDigits),
        masked: '',
        init(form){
          this.masked = applyMask(this.digits);
          form?.addEventListener('submit', () => {
            this.$refs.cpf.value = onlyDigits(this.masked);
          });
          this.$refs.cpf.addEventListener('paste', (e) => {
            e.preventDefault();
            const text = (e.clipboardData || window.clipboardData).getData('text');
            this.digits = onlyDigits(text);
            this.masked = applyMask(this.digits);
          }, { passive:false });
        },
        format(force=false){
          this.digits = onlyDigits(this.masked);
          this.masked = applyMask(this.digits);
          if(force) this.masked = applyMask(this.digits);
        }
      }
    }
  </script>
@endsection
