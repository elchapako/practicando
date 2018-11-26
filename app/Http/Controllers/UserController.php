<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        if (request()->has('empty')){
            $users = [];
        }else{
            $users = [
                'Edwin','Nestor','Marcia','Adolfo','Moises',
            ];
        }

        $title = "Listado de usuarios";

        return view('users', compact('title', 'users'));
    }

    public function show($id)
    {
        return "Mostrando detalle de usuario: {$id}";
    }

    public function create()
    {
        return 'Crear nuevo usuario';
    }
}
