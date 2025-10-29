# Estructura base para una API con Laravel 12

Esta guía resume una estructura inicial para construir una API en Laravel 12. Incluye la organización de carpetas, configuraciones clave y fragmentos de código de referencia.

## Árbol de directorios sugerido

```text
app/
  Console/
  Exceptions/
  Http/
    Controllers/
      API/
        V1/
          HealthCheckController.php
          PostController.php
    Middleware/
  Models/
    Post.php
bootstrap/
  app.php
config/
  app.php
  database.php
database/
  factories/
    PostFactory.php
  migrations/
    2024_01_01_000000_create_posts_table.php
  seeders/
    DatabaseSeeder.php
routes/
  api.php
storage/
  logs/
  framework/
    cache/
    sessions/
    views/
```

Esta estructura se basa en el esqueleto oficial de Laravel 12 con algunos ajustes orientados a APIs.

## Configuración mínima

1. Configura el archivo `composer.json` para apuntar a Laravel 12 y sus dependencias principales.
2. Desactiva el middleware CSRF en `App\Http\Middleware\VerifyCsrfToken` o utiliza grupos de middleware separados para las rutas de API.
3. Configura el archivo `config/cors.php` para permitir solicitudes desde el cliente deseado.
4. Asegura que `routes/api.php` utilice el middleware `api` y, cuando corresponda, el middleware `auth:sanctum`.

## Rutas de ejemplo (`routes/api.php`)

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\HealthCheckController;
use App\Http\Controllers\API\V1\PostController;

Route::prefix('v1')->group(function () {
    Route::get('health', [HealthCheckController::class, 'show']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('posts', PostController::class);
    });
});
```

## Controladores de ejemplo

### `HealthCheckController`

```php
<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class HealthCheckController extends Controller
{
    public function show(): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
```

### `PostController`

```php
<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PostController extends Controller
{
    public function index(): ResourceCollection
    {
        return PostResource::collection(Post::query()->latest()->paginate());
    }

    public function store(StorePostRequest $request): JsonResponse
    {
        $post = Post::create($request->validated());

        return response()->json(new PostResource($post), 201);
    }

    public function show(Post $post): JsonResponse
    {
        return response()->json(new PostResource($post));
    }

    public function update(UpdatePostRequest $request, Post $post): JsonResponse
    {
        $post->update($request->validated());

        return response()->json(new PostResource($post));
    }

    public function destroy(Post $post): JsonResponse
    {
        $post->delete();

        return response()->json(null, 204);
    }
}
```

## Modelo de ejemplo (`app/Models/Post.php`)

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'body',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];
}
```

## Migración de ejemplo

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('body');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
```

## Seeder de ejemplo

```php
<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Post::factory()->count(10)->create();
    }
}
```

## Factory de ejemplo

```php
<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(6),
            'body' => $this->faker->paragraphs(3, true),
            'published_at' => $this->faker->optional()->dateTime(),
        ];
    }
}
```

## Configuración de CORS (`config/cors.php`)

```php
<?php

return [
    'paths' => ['api/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['*'],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];
```

## Bootstrap (`bootstrap/app.php`)

```php
<?php

use Illuminate\Foundation\Application;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
    )
    ->withMiddleware(function (Application $app) {
        $app->middleware([
            // Middleware globales
        ]);

        $app->routeMiddleware([
            // 'auth' => \App\Http\Middleware\Authenticate::class,
        ]);
    })
    ->withExceptions(function (Application $app) {
        // Configuración de excepciones
    })
    ->create();
```

## Siguientes pasos

1. Instalar dependencias con Composer (`composer install`).
2. Configurar las variables de entorno en `.env`.
3. Ejecutar las migraciones y seeders (`php artisan migrate --seed`).
4. Configurar autenticación con Laravel Sanctum u otro sistema según tus necesidades.

Esta base proporciona un punto de partida limpio y organizado para una API RESTful en Laravel 12.
