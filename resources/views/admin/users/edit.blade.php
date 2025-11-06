@extends('console.layout')
@section('title','Editar Pessoa')
@section('page.title','Editar Pessoa')

@section('content')
  @include('admin.users._form', [
    'action' => route('admin.users.update', $user),
    'method' => 'PUT',
    'roles'  => $roles,
    'user'   => $user,
  ])
@endsection
