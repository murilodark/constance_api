# Constance API - AI Coding Guidelines

## Project Overview
This is a Laravel-based mini ERP API for order management, designed for suppliers. It uses Laravel 12+, Docker, MySQL 8, Redis for caching, and asynchronous job processing. The system supports admin and vendor user roles with REST API communication.

## Architecture
- **API Structure**: Versioned routes under `routes/api/v1/` with automatic loading from `public/` (unauthenticated) and `private/` (Sanctum-authenticated) subfolders. Note: Route prefixes in files like `users.php` may incorrectly include 'v1/', resulting in double versioning (e.g., `/api/v1/public/v1/users`); correct to just resource names like 'users'.
- **Data Flow**: Controllers extend base `Controller` class which uses `TraitReturnJsonOlirum` for standardized JSON responses (`{data, message, status: bool, code: int}`).
- **User Model**: Includes `status` (ativo/inativo) and `tipo` (admin/vendedor) enums; scopes like `scopeAtivo()`, `scopeAdmin()`, `scopeVendedor()`.
- **Services**: Docker-compose orchestrates `app` (PHP-FPM), `nginx` (port 8989), `db` (MySQL port 3305), and `redis`.
- **Async Processing**: Leverages Laravel Jobs for background tasks and Scheduler for cron jobs.

## Development Workflow
- **Setup**: Run `docker-compose up -d` to start services. Access app container with `docker exec -it constance_api-app-1 bash`.
- **Dependencies**: Install via Composer inside container (`cd api_laravel`). Adjust permissions: `chown -R www-data:www-data /var/www/api_laravel` and `chmod -R 775 storage bootstrap/cache`.
- **Commands**: Use `php artisan` for migrations (`migrate`), seeders, etc. Route listing: `php artisan route:list`.
- **Testing**: PHPUnit configured; run tests with `php artisan test`. Example: `php artisan test --filter=UserRegistrationTest`.
- **Debugging**: Xdebug enabled via `XDEBUG_MODE` env var in docker-compose.

## Key Patterns
- **Route Definition**: Place route files in `routes/api/v1/public/` or `private/`. Example: `auth.php` for authentication endpoints (currently commented).
- **Response Handling**: Always use `TraitReturnJsonOlirum::ReturnJson($data, $message, $status, $code)` for API responses. See `app/Traits/TraitReturnJsonOlirum.php`.
- **Authentication**: Sanctum tokens for private routes. Handle `RouteNotFoundException` as 401 for API requests.
- **Caching**: Use Redis for product caching and session storage.
- **Migrations/Seeders**: Standard Laravel, run via Artisan. User table extended with status/tipo via migration `2025_12_28_022846_add_status_and_tipo_to_users_table.php`.

## Integration Points
- **Database**: MySQL with persistent volume at `docker/mysql/`.
- **Cache/Queue**: Redis for caching and job queues.
- **Frontend**: Planned Vue.js integration; current focus is API-only.
- **External Deps**: Laravel Sanctum for auth; planned packages like `spatie/laravel-query-builder` for API filtering, `fruitcake/laravel-cors` for CORS handling (not yet installed).

Reference: [README.md](README.md) for tech stack, [docker-compose.yml](docker-compose.yml) for services, [routes/api.php](api_laravel/routes/api.php) for route loading logic, [app/Traits/TraitReturnJsonOlirum.php](api_laravel/app/Traits/TraitReturnJsonOlirum.php) for response trait, [app/Models/User.php](api_laravel/app/Models/User.php) for user model.</content>
<parameter name="filePath">d:\projetos\constance\constance_api\.github\copilot-instructions.md