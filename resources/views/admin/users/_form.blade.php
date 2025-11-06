@props(['action','method' => 'POST','roles' => collect([]),'user' => null])

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

  {{-- Contato/Identificação --}}
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

  {{-- Papel --}}
    <div>
    <label class="block text-sm mb-2">Papel</label>

    @php
        $assignedRole = isset($user) && $user ? ($user->roles->pluck('name')->first() ?? null) : null;
        $currentRole = old('role', $assignedRole);
    @endphp

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
        @foreach ($roles as $r)
        <label class="flex items-center gap-2 rounded-lg border panel px-3 py-2 cursor-pointer hover:opacity-90"
                style="border-color:var(--line)">
            <input type="radio" name="role" value="{{ $r->name }}"
                {{ $currentRole === $r->name ? 'checked' : '' }}>
            <span>{{ $r->name }}</span>
        </label>
        @endforeach
    </div>
    @error('role')<div class="text-rose-500 text-sm mt-1">{{ $message }}</div>@enderror
    </div>


  <div class="pt-1">
    <button class="brand text-white rounded-lg px-4 h-10">Salvar</button>
    <a href="{{ route('admin.users.index') }}"
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
    let out = '';
    if (d.length <= 3) out = d;
    else if (d.length <= 6) out = d.slice(0,3)+'.'+d.slice(3);
    else if (d.length <= 9) out = d.slice(0,3)+'.'+d.slice(3,6)+'.'+d.slice(6);
    else out = d.slice(0,3)+'.'+d.slice(3,6)+'.'+d.slice(6,9)+'-'+d.slice(9);
    return out;
  };

  const cpfInput = document.querySelector('input[name="cpf"][data-cpf]');
  if (!cpfInput) return;

  // Ajusta visual ao carregar (quando vem 11 dígitos do back)
  const bootVal = onlyDigits(cpfInput.value);
  if (bootVal.length === 11) cpfInput.value = maskCpf(bootVal);

  cpfInput.addEventListener('input', () => {
    const pos = cpfInput.selectionStart;
    const before = cpfInput.value;
    cpfInput.value = maskCpf(before);
    // Melhor experiência: joga o cursor ao fim
    cpfInput.setSelectionRange(cpfInput.value.length, cpfInput.value.length);
  });

  cpfInput.addEventListener('paste', () => {
    setTimeout(() => cpfInput.value = maskCpf(cpfInput.value), 0);
  });

  // Antes de enviar o form: envia somente dígitos
  cpfInput.form?.addEventListener('submit', () => {
    cpfInput.value = onlyDigits(cpfInput.value);
  });
})();
</script>
@endpush
