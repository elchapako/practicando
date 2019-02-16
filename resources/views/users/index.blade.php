@extends('layout')

@section('title', 'Usuarios')

@section('content')

    <h1>{{ $title }}</h1>

            <ul>
                @forelse ($users as $user)
                    <li>
                        {{ $user->name }}, ({{ $user->email }})
                        <a href="{{ route('users.show', ['id' => $user->id]) }}">Ver detalles</a>
                    </li>
                @empty
                    <p>No hay usuarios registrados.</p>
                @endforelse
            </ul>
@endsection

@section('sidebar')
    @parent

@endsection