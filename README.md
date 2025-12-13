<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Sistema de Gestión de Inclusión (SIP)

Sistema de gestión para la inclusión de estudiantes en instituciones educativas, desarrollado con Laravel.

## Requisitos del Sistema

Antes de instalar el proyecto, asegúrate de tener instalado:

- **PHP** >= 8.2
- **Composer** (gestor de dependencias de PHP)
- **Node.js** >= 18.x y **npm** (para compilar assets)
- **MySQL** >= 8.0 o **MariaDB** >= 10.3
- **Git**

## Instalación

### 1. Clonar el repositorio

```bash
git clone https://github.com/tu-usuario/Proyecto_SIP.git
cd Proyecto_SIP
```

### 2. Instalar dependencias de PHP

```bash
composer install
```

### 3. Configurar el archivo de entorno

Copia el archivo de ejemplo y configura las variables de entorno:

```bash
copy .env.example .env
```

En Windows PowerShell:
```powershell
Copy-Item .env.example .env
```

Edita el archivo `.env` y configura:
- `DB_CONNECTION=mysql`
- `DB_HOST=127.0.0.1`
- `DB_PORT=3306`
- `DB_DATABASE=nombre_de_tu_base_de_datos`
- `DB_USERNAME=tu_usuario_mysql`
- `DB_PASSWORD=tu_contraseña_mysql`
- `APP_URL=http://localhost:8000`

### 4. Generar la clave de aplicación

```bash
php artisan key:generate
```

### 5. Crear la base de datos

Crea una base de datos MySQL con el nombre que especificaste en el archivo `.env`:

```sql
CREATE DATABASE nombre_de_tu_base_de_datos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 6. Ejecutar las migraciones

```bash
php artisan migrate
```

### 7. Crear el enlace simbólico para el almacenamiento

```bash
php artisan storage:link
```

### 8. Instalar dependencias de Node.js

```bash
npm install
```

### 9. Compilar los assets

Para desarrollo:
```bash
npm run dev
```

Para producción:
```bash
npm run build
```

### 10. Iniciar el servidor de desarrollo

```bash
php artisan serve
```

El proyecto estará disponible en: `http://localhost:8000`

## Configuración Adicional

### Crear un usuario administrador

Para crear un usuario administrador, puedes usar Tinker:

```bash
php artisan tinker
```

Luego ejecuta:
```php
User::create([
    'nombre' => 'Admin',
    'apellido' => 'Sistema',
    'email' => 'admin@inacap.cl',
    'password' => bcrypt('contraseña_segura'),
    'rol_id' => 1, // Ajusta según el ID del rol de administrador
]);
```

### Poblar la base de datos (Opcional)

Si tienes seeders configurados:

```bash
php artisan db:seed
```

## Estructura del Proyecto

- `app/` - Lógica de la aplicación (Controladores, Modelos, etc.)
- `database/` - Migraciones y seeders
- `resources/views/` - Vistas Blade
- `routes/` - Definición de rutas
- `public/` - Archivos públicos accesibles
- `storage/` - Archivos subidos y logs

## Tecnologías Utilizadas

- **Laravel 12** - Framework PHP
- **MySQL** - Base de datos
- **Bootstrap 5** - Framework CSS
- **Chart.js** - Gráficos y visualizaciones
- **AdminLTE 3** - Panel de administración
- **Vite** - Build tool para assets

## Desarrollo

### Compilar assets en modo desarrollo (con watch)

```bash
npm run dev
```

### Ejecutar tests

```bash
php artisan test
```

## Licencia

Este proyecto es software de código abierto licenciado bajo la [licencia MIT](https://opensource.org/licenses/MIT).

---

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
