@extends('console.layout')
@section('title','Editar Revisor')
@section('page.title','Editar Revisor')

@section('content')
  @include('coordenador.revisores._form', [
    'action' => route('coordenador.revisores.update', $user),
    'method' => 'PUT',
    'user'   => $user,
    'categories' => $categories,
    'selected'   => $selected,
  ])
@endsection
