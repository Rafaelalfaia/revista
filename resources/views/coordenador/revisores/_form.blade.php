@props([
  'action',
  'method' => 'POST',
  'user' => null,
  'categories' => collect([]),
  'selected' => [],
])

@push('head')
<style>
  .rev-form-shell{display:flex;flex-direction:column;gap:1rem;max-width:720px}
  .rev-form-card{border-radius:1.3rem;border:1px solid var(--line);background:
    radial-gradient(circle at top left,rgba(52,211,153,.12),transparent 55%),
    radial-gradient(circle at top right,rgba(59,130,246,.14),transparent 55%),
    var(--panel);padding:.95rem 1rem 1rem;display:flex;flex-direction:column;gap:.75rem}
  .rev-form-header{display:flex;justify-content:space-between;gap:.75rem;align-items:flex-start}
  .rev-form-title{font-size:.95rem;font-weight:700}
  .rev-form-sub{font-size:.8rem;color:var(--muted);margin-top:.1rem}
  .rev-form-badge{border-radius:999px;padding:.16rem .6rem;font-size:.7rem;border:1px solid var(--line);background:var(--panel-2)}
  .rev-form-body{margin-top:.35rem}
  .field-group{display:grid;gap:.4rem}
  .field-label{font-size:.8rem;font-weight:500}
  .field-hint{font-size:.7rem;color:var(--muted);margin-top:.1rem}
  .field-input{width:100%;border-radius:.9rem;border:1px solid var(--line);background:var(--panel-2);padding:.6rem .8rem;font-size:.8rem}
  .field-error{font-size:.75rem;color:rgb(248,113,113);margin-top:.15rem}
  .grid-two{display:grid;gap:.7rem}
  @media(min-width:640px){.grid-two{grid-template-columns:repeat(2,minmax(0,1fr))}}
  .categories-shell{border-radius:1rem;border:1px solid var(--line);background:var(--panel-2);padding:.4rem .4rem .5rem;max-height:280px;overflow:auto}
  .cat-item{display:flex;align-items:center;gap:.4rem;border-radius:.7rem;padding:.25rem .45rem;font-size:.8rem}
  .cat-item input{accent-color:var(--brand)}
  .cat-empty{font-size:.75rem;color:var(--muted);padding:.25rem .3rem}
  .actions-row{display:flex;flex-wrap:wrap;gap:.5rem;margin-top:.4rem}
  .btn-primary{border-radius:.9rem;padding:.55rem 1.1rem;font-size:.8rem;font-weight:600;display:inline-flex;align-items:center;gap:.3rem;background:var(--brand);color:#fff;border:none}
  .btn-secondary{border-radius:.9rem;padding:.5rem 1rem;font-size:.8rem;font-weight:500;display:inline-flex;align-items:center;gap:.3rem;border:1px solid var(--line);background:var(--panel-2)}
</style>
@endpush

<div class="rev-form-shell">
  <div class="rev-form-card">
    <div class="rev-form-header">
      <div>
        <div class="rev-form-title">
          {{ $user ? 'Editar revisor' : 'Novo revisor' }}
        </div>
        <div class="rev-form-sub">
          Defina dados de acesso e as áreas em que este revisor poderá receber submissões.
        </div>
      </div>
      <span class="rev-form-badge">
        {{ $user ? 'Revisor existente' : 'Novo cadastro' }}
      </span>
    </div>

    <div class="rev-form-body">
      <form method="POST" action="{{ $action }}" class="space-y-5" x-data>
        @csrf
        @if (strtoupper($method) !== 'POST') @method($method) @endif

        <div class="field-group">
          <label class="field-label">Nome</label>
          <input
            name="name"
            value="{{ old('name', $user->name ?? '') }}"
            class="field-input"
          >
          @error('name')
            <div class="field-error">{{ $message }}</div>
          @enderror
        </div>

        <div class="grid-two">
          <div class="field-group">
            <label class="field-label">E-mail (opcional)</label>
            <input
              type="email"
              name="email"
              autocomplete="email"
              value="{{ old('email', $user->email ?? '') }}"
              class="field-input"
            >
            @error('email')
              <div class="field-error">{{ $message }}</div>
            @enderror
          </div>

          <div class="field-group">
            <label class="field-label">CPF (opcional)</label>
            <input
              name="cpf"
              inputmode="numeric"
              maxlength="14"
              value="{{ old('cpf', $user->cpf_formatted ?? $user->cpf ?? '') }}"
              data-cpf
              class="field-input"
            >
            @error('cpf')
              <div class="field-error">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <div class="field-hint">
          Informe <strong>e-mail ou CPF</strong>. Pelo menos um desses campos é obrigatório.
        </div>

        <div class="grid-two">
          <div class="field-group">
            <label class="field-label">
              {{ $user ? 'Nova senha (opcional)' : 'Senha' }}
            </label>
            <input
              type="password"
              name="password"
              autocomplete="new-password"
              class="field-input"
            >
            @error('password')
              <div class="field-error">{{ $message }}</div>
            @enderror
          </div>

          <div class="field-group">
            <label class="field-label">Confirmar senha</label>
            <input
              type="password"
              name="password_confirmation"
              autocomplete="new-password"
              class="field-input"
            >
          </div>
        </div>

        <div class="field-group">
          <label class="field-label">Áreas do revisor (categorias)</label>
          <div class="categories-shell">
            @forelse ($categories as $c)
              <label class="cat-item cursor-pointer hover:opacity-90">
                <input
                  type="checkbox"
                  name="categories[]"
                  value="{{ $c->id }}"
                  {{ in_array($c->id, old('categories', $selected ?? []), true) ? 'checked' : '' }}
                >
                <span>{{ $c->name }}</span>
              </label>
            @empty
              <div class="cat-empty">
                Nenhuma categoria cadastrada. Cadastre categorias antes de atribuir áreas aos revisores.
              </div>
            @endforelse
          </div>
          @error('categories')
            <div class="field-error">{{ $message }}</div>
          @enderror
        </div>

        <div class="actions-row">
          <button class="btn-primary">
            <span>Salvar</span>
          </button>
          <a
            href="{{ route('coordenador.revisores.index') }}"
            class="btn-secondary"
          >
            <span>Cancelar</span>
          </a>
        </div>
      </form>
    </div>
  </div>
</div>

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
