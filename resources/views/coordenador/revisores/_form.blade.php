@props([
  'action',
  'method' => 'POST',
  'user' => null,
  'categories' => collect([]),
  'selected' => [],
])

<form method="POST" action="{{ $action }}" class="space-y-5" x-data>
  @csrf
  @if (strtoupper($method) !== 'POST') @method($method) @endif

  {{-- Nome --}}
  <div>
    <label class="block text-sm mb-1">Nome</label>
    <input name="name" value="{{ old('name', $user->name ?? '') }}"
           class="w-full rounded-lg border panel h-10 px-3 bg-transparent
                  focus:outline-none focus:ring-2 focus:ring-rose-500/60"
           style="border-color:var(--line)">
    @error('name')<div class="text-rose-500 text-sm mt-1">{{ $message }}</div>@enderror
  </div>

  {{-- E-mail / CPF --}}
  <div class="grid sm:grid-cols-2 gap-4">
    <div>
      <label class="block text-sm mb-1">E-mail (opcional)</label>
      <input type="email" name="email" autocomplete="email"
             value="{{ old('email', $user->email ?? '') }}"
             class="w-full rounded-lg border panel h-10 px-3 bg-transparent
                    focus:outline-none focus:ring-2 focus:ring-rose-500/60"
             style="border-color:var(--line)">
      @error('email')<div class="text-rose-500 text-sm mt-1">{{ $message }}</div>@enderror
    </div>

    <div>
      <label class="block text-sm mb-1">CPF (opcional)</label>
      <input name="cpf" inputmode="numeric" maxlength="14"
             value="{{ old('cpf', $user->cpf_formatted ?? $user->cpf ?? '') }}"
             data-cpf
             class="w-full rounded-lg border panel h-10 px-3 bg-transparent
                    focus:outline-none focus:ring-2 focus:ring-rose-500/60"
             style="border-color:var(--line)">
      @error('cpf')<div class="text-rose-500 text-sm mt-1">{{ $message }}</div>@enderror
    </div>
  </div>
  <p class="text-xs muted -mt-2">Informe <strong>e-mail ou CPF</strong> (um dos dois é obrigatório).</p>

  {{-- Senha --}}
  <div class="grid sm:grid-cols-2 gap-4">
    <div>
      <label class="block text-sm mb-1">{{ $user ? 'Nova senha (opcional)' : 'Senha' }}</label>
      <input type="password" name="password" autocomplete="new-password"
             class="w-full rounded-lg border panel h-10 px-3 bg-transparent
                    focus:outline-none focus:ring-2 focus:ring-rose-500/60"
             style="border-color:var(--line)">
      @error('password')<div class="text-rose-500 text-sm mt-1">{{ $message }}</div>@enderror
    </div>
    <div>
      <label class="block text-sm mb-1">Confirmar senha</label>
      <input type="password" name="password_confirmation" autocomplete="new-password"
             class="w-full rounded-lg border panel h-10 px-3 bg-transparent
                    focus:outline-none focus:ring-2 focus:ring-rose-500/60"
             style="border-color:var(--line)">
    </div>
  </div>

  {{-- Categorias (áreas do revisor) --}}
  <div>
    <label class="block text-sm mb-2">Áreas do revisor (categorias)</label>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-2 max-h-[280px] overflow-auto p-1 rounded-lg panel border"
         style="border-color:var(--line)">
      @foreach ($categories as $c)
        <label class="flex items-center gap-2 rounded-md px-2 py-1 hover:opacity-90 cursor-pointer">
          <input type="checkbox" name="categories[]" value="{{ $c->id }}"
                 {{ in_array($c->id, old('categories', $selected ?? []), true) ? 'checked' : '' }}>
          <span class="text-sm">{{ $c->name }}</span>
        </label>
      @endforeach
    </div>
    @error('categories')<div class="text-rose-500 text-sm mt-1">{{ $message }}</div>@enderror
  </div>

  <div class="pt-1">
    <button class="brand text-white rounded-lg px-4 h-10">Salvar</button>
    <a href="{{ route('coordenador.revisores.index') }}"
       class="ml-2 rounded-lg px-4 h-10 inline-flex items-center border panel hover:opacity-90"
       style="border-color:var(--line)">Cancelar</a>
  </div>
</form>

@push('scripts')
<script>
(function () {
  const onlyDigits = s => (s || '').replace(/\D+/g, '');
  const maskCpf = d => {
    d = onlyDigits(d).slice(0, 11);
    if (!d) return '';
    if (d.length <= 3) return d;
    if (d.length <= 6) return d.slice(0,3)+'.'+d.slice(3);
    if (d.length <= 9) return d.slice(0,3)+'.'+d.slice(3,6)+'.'+d.slice(6);
    return d.slice(0,3)+'.'+d.slice(3,6)+'.'+d.slice(6,9)+'-'+d.slice(9);
  };

  const cpf = document.querySelector('input[name="cpf"][data-cpf]');
  if (!cpf) return;

  const boot = onlyDigits(cpf.value);
  if (boot.length === 11) cpf.value = maskCpf(boot);

  cpf.addEventListener('input', () => {
    cpf.value = maskCpf(cpf.value);
    cpf.setSelectionRange(cpf.value.length, cpf.value.length);
  });
  cpf.addEventListener('paste', () => setTimeout(() => cpf.value = maskCpf(cpf.value), 0));
  cpf.form?.addEventListener('submit', () => { cpf.value = onlyDigits(cpf.value); });
})();
</script>
@endpush
