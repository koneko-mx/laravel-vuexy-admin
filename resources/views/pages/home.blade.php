@php
    $configData = Helper::appClasses();
@endphp

@extends('vuexy-admin::layouts.vuexy.layoutMaster')

@section('content')

<div class="row">
    <h4>{{ $_admin['title'] }}</h4>
    <p>Para mayor información al respecto consulta la <a href="{{ config('koneko.documentation') }}" target="_blank" rel="noopener noreferrer">documentación</a>.</p>

    @php
        use Illuminate\Support\Facades\Auth;

        // Obtener el usuario autenticado
        $user = Auth::user();

        echo '<pre>';
            if ($user) {
                // Imprimir información del usuario
                echo "Usuario: {$user->name}\n";
                echo "Email: {$user->email}\n\n";

                // Obtener todos los roles del usuario
                $roles = $user->roles;

                // Iterar sobre los roles del usuario
                foreach ($roles as $role) {
                    echo "Rol: {$role->name}\n";

                    // Obtener todos los permisos del rol
                    $permissions = $role->permissions;

                    // Imprimir los permisos del rol
                    foreach ($permissions as $permission) {
                        echo " - Permiso: {$permission->name}\n";
                    }

                    echo "\n";
                }

            } else {
                echo "Usuario no autenticado\n";
            }
        echo '</pre>';
    @endphp

@endsection
