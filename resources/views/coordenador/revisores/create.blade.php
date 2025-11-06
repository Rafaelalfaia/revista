@extends('console.layout')
@section('title','Novo Revisor')
@section('page.title','Novo Revisor')

@section('content')
  @include('coordenador.revisores._form', [
    'action' => route('coordenador.revisores.store'),
    'method' => 'POST',
    'user'   => null,
    'categories' => $categories,
    'selected'   => [],
  ])
@endsection
