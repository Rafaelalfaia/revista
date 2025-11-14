<option value="">Todas as categorias</option>
@foreach(($categories ?? []) as $cat)
  <option value="{{ $cat->id }}" {{ (int)request('category_id') === (int)$cat->id ? 'selected' : '' }}>
    {{ $cat->name }}
  </option>
@endforeach
