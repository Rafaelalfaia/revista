@extends('console.layout')

@section('title','Editar revisor')
@section('page.title','Editar revisor')

@section('content')
  @include('coordenador.revisores._form', [
    'action'     => route('coordenador.revisores.update', $user),
    'method'     => 'PUT',
    'user'       => $user,
    'categories' => $categories,
    'selected'   => $selected,
  ])
@endsection
