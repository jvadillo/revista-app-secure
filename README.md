
# Proyecto de ejemplo para probar la Autorización de Laravel

## Prerequisitos

1. Crea un nuevo proyecto de Laravel.
2. Instala la autenticación con Laravel Breeze. 

## Pasos que se han seguido

1. Crea una nueva migración que añada a la tabla `users` una nueva columna llamada `role` de tipo `enum`:

```
php artisan make:migration add_role_column_to_users_table
```

Añade la columna a la nueva migración:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRoleColumnToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        // Add roles to user table
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role',  ['admin', 'editor', 'user']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
}
```

2. Lanza la nueva migración junto con la creación de datos mediante el seeder:

```
sail artisan migrate:fresh --seed
```

3. Crea un nuevo Seeder para crear usuarios de los tres tipos: `admin`, `editor` y `user`.

4. Crea el controlador `UserController`:

```
Route::get('/users', 'UserController@index')->name('user.index');
```

5. Crea el controlador `UserController`:

```
php artisan make:controller UserController
```

El método `index` devolverá la vista únicamente si el usuario tiene permisos para ello.

```php
class UserController extends Controller
{
    public function index (Request $request)
    {
        if (! Gate::allows('view-users', $articulo)) {
            abort(403);
        }
        return view('users.index');
    }
}
```

6. Crea el `Gate` para la visualización de usuarios:

```php
<?php

public function boot()
{
    $this->registerPolicies();

    Gate::define('view-users', function (User $user) {
        $authorized_roles = ['admin', 'editor'];
        return in_array($user->role, $authorized_roles);
    });

}
```

7. Por último, crear la vista que contiene el listado de usuarios. La vista realiza acciones interesantes como las siguientes:
- Mostrar el nombre del usuario conectado mediante `{{ Auth::user()->name }}`.
- Mostrar información al usuario en función de sus permisos, utilizando la directiva `@can`

```html
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
```