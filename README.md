
# Autorización de Laravel: proyecto de ejemplo

El código de este proyecto contiene una aplicación con autenticación implementada (mediante Laravel Breeze) y algunos ejemplos de permisos y autorización. En este documento se explicarán los pasos que se han dado para realizarlo.

## Prerequisitos

1. Crea un nuevo proyecto de Laravel
2. Instala la autenticación con Laravel Breeze. 

Tienes toda la información necesaria para realizar estos dos pasos en [https://laravel.jonvadillo.com](https://laravel.jonvadillo.com)

## Guía paso a paso

1. La aplicación tendrá usuarios con 3 posibles roles: `admin`, `editor` o `user`. Por lo tanto, crea una nueva migración que añada a la tabla `users` una nueva columna llamada `role` de tipo `enum`:

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
2. Modifica el `DatabaseSeeder` que viene por defecto para que cree usuarios de los 3 tipos: `admin`, `editor` y `user`.

```php
<?php

namespace Database\Seeders;
use App\Models\User;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Create admin user
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@egibide.org',
            'password' => bcrypt('12345Abcde'),
            'role' => 'admin'
        ]);

        // Create a editor user
        User::factory()->create([
            'name' => 'Editor User',
            'email' => 'editor@egibide.org',
            'password' => bcrypt('12345Abcde'),
            'role' => 'editor'
        ]);

        // Create 20 users that are normal users
        User::factory(20)->create(['role' => 'user']);
    }
}
```

3. Lanza la nueva migración junto con la creación de datos mediante el seeder:

```
sail artisan migrate:fresh --seed
```

4. La aplicación mostrará una vista con el listado de usuarios. Para ello, comienza por crear la nueva ruta modificando el archivo `web.php`:

```
Route::get('/users', 'UserController@index')->name('user.index');
```

5. A continuación, crea el controlador `UserController`:

```
php artisan make:controller UserController
```

El método `index` devolverá la vista que más tarde crearemos.

```php
class UserController extends Controller
{
    public function index (Request $request)
    {
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

7. Modifica el método `index` del controlador para que no devuelva la vista si el usuario no es `admin` o `editor`:

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

8. Por último, crea la vista que contiene el listado de usuarios. La vista realiza acciones interesantes como las siguientes:
   
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