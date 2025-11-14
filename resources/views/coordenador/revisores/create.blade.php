@extends('console.layout')

@section('title','Novo revisor')
@section('page.title','Novo revisor')

@section('content')
  @include('coordenador.revisores._form', [
    'action'     => route('coordenador.revisores.store'),
    'method'     => 'POST',
    'user'       => null,
    'categories' => $categories,
    'selected'   => [],
  ])
@endsection
