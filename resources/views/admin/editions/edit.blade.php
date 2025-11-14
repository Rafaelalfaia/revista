@extends('console.layout')
@section('title','Editar edição')
@section('page.title','Editar edição')

@push('head')
<style>
  .panel{border:1px solid var(--line);background:var(--panel);border-radius:1rem}
  .input{width:100%;border:1px solid var(--line);background:var(--panel);color:var(--text);border-radius:.75rem;padding:.55rem .75rem}
  .btn{border:1px solid var(--line);background:var(--panel);color:var(--text);border-radius:.75rem;padding:.6rem 1rem;font-weight:700}
  .btn-brand{background:var(--brand);color:white;border-color:transparent}
  .muted{color:var(--muted)}
</style>
@endpush

@section('page.actions')
  <a href="{{ route('admin.editions.submissions.index',$edition) }}" class="px-3 py-2 rounded-lg bg-[var(--brand)] text-white font-semibold">Gerenciar publicações</a>
@endsection

@section('content')
  @if(session('ok')) <div class="mb-4 text-sm rounded-md p-3 bg-emerald-600/10 border border-emerald-600/30 text-emerald-600">{{ session('ok') }}</div> @endif
  @if ($errors->any())
    <div class="mb-4 text-sm rounded-md p-3 bg-rose-600/10 border border-rose-600/30 text-rose-600">{{ $errors->first() }}</div>
  @endif

  <form x-data="edForm()" x-init="init()" method="POST" action="{{ route('admin.editions.update',$edition) }}" enctype="multipart/form-data" class="grid gap-4 lg:grid-cols-3">
    @csrf @method('PUT')
    <div class="panel p-4 lg:col-span-2 grid gap-4">
      <div class="grid md:grid-cols-2 gap-3">
        <div>
          <label class="text-sm muted">Título</label>
          <input class="input" type="text" name="title" x-model="title" value="{{ $edition->title }}" required>
        </div>
        <div>
          <label class="text-sm muted">Slug</label>
          <input class="input" type="text" name="slug" x-model="slug" value="{{ $edition->slug }}">
        </div>
      </div>
      <div>
        <label class="text-sm muted">Descrição</label>
        <textarea class="input min-h-[140px]" name="description">{{ $edition->description }}</textarea>
      </div>
      <div class="grid md:grid-cols-2 gap-3">
        <div>
          <label class="text-sm muted">Data de lançamento</label>
          <input class="input" type="date" name="release_date" value="{{ optional($edition->release_date)->format('Y-m-d') }}">
        </div>
        <div>
          <label class="text-sm muted">Publicado em</label>
          <input class="input" type="datetime-local" name="published_at" value="{{ $edition->published_at ? $edition->published_at->format('Y-m-d\TH:i') : '' }}">
        </div>
      </div>
    </div>

    <div class="panel p-4 grid gap-5">
      <div>
        <label class="text-sm muted">Foto de perfil</label>
        <input class="input" type="file" name="profile_photo" accept="image/*" @change="preview($event,'profile')">
        <div class="mt-2 rounded-lg overflow-hidden border border-[var(--line)] bg-[var(--soft)] h-36 grid place-items-center">
          @php $profileUrl = $edition->profile_photo_path ? Storage::disk($edition->profile_photo_disk ?? 'public')->url($edition->profile_photo_path) : null; @endphp
          <img x-show="profile || '{{ $profileUrl }}'" :src="profile || '{{ $profileUrl }}'" class="w-full h-full object-cover">
          <div x-show="!profile && !'{{ $profileUrl }}'" class="text-xs muted">sem imagem</div>
        </div>
        @if($edition->profile_photo_path)
          <label class="flex items-center gap-2 mt-2 text-sm"><input type="checkbox" name="remove_profile_photo" value="1"> Remover foto de perfil</label>
        @endif
      </div>

      <div>
        <label class="text-sm muted">Foto de capa</label>
        <input class="input" type="file" name="cover_photo" accept="image/*" @change="preview($event,'cover')">
        <div class="mt-2 rounded-lg overflow-hidden border border-[var(--line)] bg-[var(--soft)] h-36 grid place-items-center">
          @php $coverUrl = $edition->cover_photo_path ? Storage::disk($edition->cover_photo_disk ?? 'public')->url($edition->cover_photo_path) : null; @endphp
          <img x-show="cover || '{{ $coverUrl }}'" :src="cover || '{{ $coverUrl }}'" class="w-full h-full object-cover">
          <div x-show="!cover && !'{{ $coverUrl }}'" class="text-xs muted">sem imagem</div>
        </div>
        @if($edition->cover_photo_path)
          <label class="flex items-center gap-2 mt-2 text-sm"><input type="checkbox" name="remove_cover_photo" value="1"> Remover foto de capa</label>
        @endif
      </div>

      <div class="flex items-center gap-3">
        <button class="btn btn-brand">Salvar alterações</button>
        <form method="POST" action="{{ route('admin.editions.destroy',$edition) }}" onsubmit="return confirm('Remover esta edição?')">
          @csrf @method('DELETE')
          <button class="btn">Excluir edição</button>
        </form>
      </div>
    </div>
  </form>

  <script>
    function edForm(){
      return {
        title:@js($edition->title), slug:@js($edition->slug), profile:null, cover:null,
        init(){ this.$watch('title', v => { if(!this.slug) this.slug = v.toLowerCase().trim().replace(/\s+/g,'-').replace(/[^a-z0-9\-]/g,''); }); },
        preview(e,which){ const f=e.target.files?.[0]; if(!f)return; const url=URL.createObjectURL(f); this[which]=url; }
      }
    }
  </script>
@endsection
