<option value="">Todas as edições</option>
@foreach(($editions ?? []) as $ed)
  <option value="{{ $ed->id }}" {{ (int)request('edition_id') === (int)$ed->id ? 'selected' : '' }}>
    {{ $ed->title }} @if(!empty($ed->release_date)) ({{ \Illuminate\Support\Carbon::parse($ed->release_date)->format('m/Y') }}) @endif
  </option>
@endforeach
