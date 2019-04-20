@extends('layout')

@section('title', "Crear usuario")

@section('content')
    <h1>Editar Usuario</h1>

    @if($errors->any())
        <div class="alert alert-danger">
            <h6>Por favor corrige los errores de abajo</h6>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{$error}}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="post" action="{{ url("usuarios/{$user->id}") }}">
        {{ method_field('put') }}
        {{ csrf_field() }}

        <label for="name">Nombre:</label>
        <input type="text" name="name" id="name" placeholder="Pedro Perez" value="{{old('name', $user->name)}}">
        <br>
        <label for="email">Correo electrónico:</label>
        <input type="email" name="email" id="email" placeholder="pedro@example.com" value="{{old('email', $user->email)}}">
        <br>
        <label for="password">Contraseña:</label>
        <input type="password" name="password" id="password" placeholder="Mayor a 6 caracteres">
        <br>
        <button type="submit">Actualizar usuario</button>
    </form>
    <p>
        <a href="{{ route('users') }}">Regresar al listado de usuarios</a>
    </p>
@endsection

