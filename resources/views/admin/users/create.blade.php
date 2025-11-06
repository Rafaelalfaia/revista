@extends('console.layout')
@section('title','Nova Pessoa')
@section('page.title','Nova Pessoa')

@section('content')
  @include('admin.users._form', [
    'action' => route('admin.users.store'),
    'method' => 'POST',
    'roles'  => $roles,
    'user'   => null,
  ])
@endsection
