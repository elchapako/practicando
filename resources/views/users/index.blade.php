@extends('layout')

@section('title', 'Usuarios')

@section('content')

    <h1>{{ $title }}</h1>

    <p>
        <a href="{{route('users.create')}}">Nuevo Usuario</a>
    </p>

            <ul>
                @forelse ($users as $user)
                    <li>
                        {{ $user->name }}, ({{ $user->email }})
                        <a href="{{ route('users.show', $user) }}">Ver detalles</a> |
                        <a href="{{ route('users.edit', $user) }}">Editar</a> |
                        <form action="{{ route('users.destroy', $user) }}" method="post">
                            {{ csrf_field() }}
                            {{ method_field('delete') }}
                            <button type="submit">Eliminar</button>
                        </form>
                    </li>
                @empty
                    <p>No hay usuarios registrados.</p>
                @endforelse
            </ul>
@endsection

@section('sidebar')
    @parent

@endsection