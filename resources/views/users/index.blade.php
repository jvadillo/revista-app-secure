<!DOCTYPE html>
<html lang="es">
<head>
    <title>Document</title>
</head>
<body>
    <h1>Listado de usuarios</h1>
    <!-- Mostrar el nombre de usuario -->
    <p>Hola {{ Auth::user()->name }}</p>
    <!-- Botón para cerrar sesión -->
    <form action="{{ url('logout') }}" method="POST">
        @csrf
        <button type="submit">Cerrar sesión</button>
    </form>
    <p>Tienes los siguientes permisos:
    <!-- Mostrar los permisos del usuario conectado -->
    <ul>
        @can('create-users')
        <li>Crear usuarios.</li>
        @endcan
        @can('view-users')
        <li>Ver usuarios.</li>
        @endcan
    </ul>
    </p>
    <h2>Listado de usuarios</h2>
    <div class="container">
        <table>
            <tr>
                <th>Usuario</th>
                <!-- Solo mostrar el email a usuarios que pueden crear usuarios -->
                @can('edit-users')
                <th>Email</th>
                @endcan
                <th>Rol</th>
            </tr>
            @foreach ($users as $user)
            <tr>
                <td>{{ ($user->name) }}</td>
                <!-- Solo mostrar el email a usuarios que pueden crear usuarios -->
                @can('edit-users')
                <td>{{ ($user->email) }}</td>
                @endcan
                <td>{{ ($user->role) }}</td>
            </tr>
            @endforeach
        </table>        
    </div>
</body>
</html>