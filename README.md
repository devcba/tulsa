# Base de API con Laravel 12

Este repositorio contiene una guía de referencia y stubs de código para levantar una API REST en Laravel 12.

## Contenido

- `docs/api-structure.md`: documentación sobre la estructura recomendada y fragmentos clave.
- `stubs/`: ejemplos de controladores, modelos, rutas, migraciones y configuración.

## Uso sugerido

1. Crea un nuevo proyecto con `composer create-project laravel/laravel nombre-proyecto "^12.0"`.
2. Copia el contenido de `stubs/` dentro del proyecto, conservando la estructura de carpetas.
3. Ejecuta `composer install`, `php artisan migrate --seed` y `php artisan test` para validar el arranque.
4. Ajusta autenticación, CORS y pruebas automatizadas según tus necesidades.
