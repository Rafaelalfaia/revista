@extends('console.layout')
@section('title','Nova edição')
@section('page.title','Nova edição')

@push('head')
<style>
  .panel{border:1px solid var(--line);background:var(--panel);border-radius:1rem}
  .input{width:100%;border:1px solid var(--line);background:var(--panel);color:var(--text);border-radius:.75rem;padding:.55rem .75rem}
  .btn{border:1px solid var(--line);background:var(--panel);color:var(--text);border-radius:.75rem;padding:.6rem 1rem;font-weight:700}
  .btn-brand{background:var(--brand);color:white;border-color:transparent}
  .muted{color:var(--muted)}
</style>
@endpush

@section('content')
  @if ($errors->any())
    <div class="mb-4 text-sm rounded-md p-3 bg-rose-600/10 border border-rose-600/30 text-rose-600">
      {{ $errors->first() }}
    </div>
  @endif

  <form x-data="edForm()" x-init="init()" method="POST" action="{{ route('admin.editions.store') }}" enctype="multipart/form-data" class="grid gap-4 lg:grid-cols-3">
    @csrf
    <div class="panel p-4 lg:col-span-2 grid gap-4">
      <div class="grid md:grid-cols-2 gap-3">
        <div>
          <label class="text-sm muted">Título</label>
          <input class="input" type="text" name="title" x-model="title" required>
        </div>
        <div>
          <label class="text-sm muted">Slug</label>
          <input class="input" type="text" name="slug" x-model="slug" placeholder="gerado automaticamente">
        </div>
      </div>
      <div>
        <label class="text-sm muted">Descrição</label>
        <textarea class="input min-h-[140px]" name="description"></textarea>
      </div>
      <div class="grid md:grid-cols-2 gap-3">
        <div>
          <label class="text-sm muted">Data de lançamento</label>
          <input class="input" type="date" name="release_date">
        </div>
        <div>
          <label class="text-sm muted">Publicado em</label>
          <input class="input" type="datetime-local" name="published_at">
        </div>
      </div>
    </div>

    <div class="panel p-4 grid gap-4">
      <div>
        <label class="text-sm muted">Foto de perfil</label>
        <input class="input" type="file" name="profile_photo" accept="image/*" @change="preview($event,'profile')">
        <div class="mt-2 rounded-lg overflow-hidden border border-[var(--line)] bg-[var(--soft)] h-36 grid place-items-center">
          <img x-show="profile" :src="profile" class="w-full h-full object-cover">
          <div x-show="!profile" class="text-xs muted">prévia</div>
        </div>
      </div>
      <div>
        <label class="text-sm muted">Foto de capa</label>
        <input class="input" type="file" name="cover_photo" accept="image/*" @change="preview($event,'cover')">
        <div class="mt-2 rounded-lg overflow-hidden border border-[var(--line)] bg-[var(--soft)] h-36 grid place-items-center">
          <img x-show="cover" :src="cover" class="w-full h-full object-cover">
          <div x-show="!cover" class="text-xs muted">prévia</div>
        </div>
      </div>
      <button class="btn btn-brand">Salvar edição</button>
    </div>
  </form>

  <script>
    function edForm(){
      return {
        title:'', slug:'', profile:null, cover:null,
        init(){ this.$watch('title', v => { if(!this.slug) this.slug = v.toLowerCase().trim().replace(/\s+/g,'-').replace(/[^a-z0-9\-]/g,''); }); },
        preview(e,which){ const f=e.target.files?.[0]; if(!f)return; const url=URL.createObjectURL(f); this[which]=url; }
      }
    }
  </script>
@endsection
