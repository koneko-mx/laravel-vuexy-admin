# 🎨 Laravel Vuexy Admin

<p align="center">
    <a href="https://koneko.mx" target="_blank"> <img src="https://git.koneko.mx/Koneko-ST/koneko-st/raw/branch/main/logo-images/horizontal-05.png" width="400" alt="Koneko Soluciones Tecnológicas Logo"> </a> 
</p>
<p align="center">
    <a href="https://koneko.mx"><img src="https://img.shields.io/badge/Website-koneko.mx-blue" alt="Sitio Web"></a> 
    <a href="https://packagist.org/packages/koneko/laravel-vuexy-admin"><img src="https://img.shields.io/packagist/v/koneko/laravel-vuexy-admin" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/koneko/laravel-vuexy-admin"><img src="https://img.shields.io/packagist/l/koneko/laravel-vuexy-admin" alt="License"></a>
    <a href="https://git.koneko.mx/koneko"><img src="https://img.shields.io/badge/Git%20Server-Koneko%20Git-orange" alt="Servidor Git"></a> 
    <a href="https://github.com/koneko-mx/laravel-vuexy-admin/actions/workflows/tests.yml"><img src="https://github.com/koneko-mx/laravel-vuexy-admin/actions/workflows/tests.yml/badge.svg" alt="Build Status"></a> 
    <a href="https://github.com/koneko-mx/laravel-vuexy-admin/issues"><img src="https://img.shields.io/github/issues/koneko-mx/laravel-vuexy-admin" alt="Issues"></a> 
</p>

---

## 📌 Descripción

**Laravel Vuexy Admin** es un módulo de administración optimizado para México, basado en Laravel 11 y diseñado para integrarse con **Vuexy Admin Template**. Incluye gestión avanzada de usuarios, roles, permisos y auditoría de acciones.

### ✨ Características
- 🔹 Sistema de autenticación con Laravel Fortify.
- 🔹 Gestión avanzada de usuarios con Livewire.
- 🔹 Control de roles y permisos con Spatie Permissions.
- 🔹 Auditoría de acciones con Laravel Auditing.
- 🔹 Publicación de configuraciones y vistas.
- 🔹 Soporte para cache y optimización de rendimiento.

---

## 📦 Instalación

### 🔹 Opción 1: Desde Packagist (Recomendado)

Instala el paquete desde [Packagist](https://packagist.org/packages/koneko/laravel-vuexy-admin):

```bash
composer require koneko/laravel-vuexy-admin
```

> Asegúrate de tener habilitado Packagist en tu `composer.json`.

### 🔹 Opción 2: Desde repositorio Git (GitHub o Tea)

También puedes instalarlo como repositorio privado en desarrollo:

```json
"repositories": {
    "koneko/laravel-vuexy-admin": {
        "type": "vcs",
        "url": "https://github.com/koneko-mx/laravel-vuexy-admin"
    }
}
```

Luego ejecuta:

```bash
composer require koneko/laravel-vuexy-admin:@dev
```

> Puedes cambiar la URL por `https://git.koneko.mx/koneko/laravel-vuexy-admin` si usas Tea como servidor Git.

---

## 🚀 Uso básico

```php
use Koneko\VuexyAdmin\Models\User;

$user = User::create([
    'name' => 'Juan Pérez',
    'email' => 'juan@example.com',
    'password' => bcrypt('secret'),
]);
```

---

## 📚 Configuración adicional

Si necesitas personalizar la configuración del módulo, publica el archivo de configuración:

```bash
php artisan vendor:publish --tag=vuexy-admin-config
```

Esto generará `config/vuexy_menu.php`, donde puedes modificar valores predeterminados.

---

## 🛠 Dependencias

Este paquete requiere las siguientes dependencias:
- Laravel 11
- `laravel/fortify` (autenticación)
- `spatie/laravel-permission` (gestión de roles y permisos)
- `owen-it/laravel-auditing` (auditoría de usuarios)
- `livewire/livewire` (interfaz dinámica)

---

## 📦 Publicación de Assets y Configuraciones

Para publicar configuraciones y seeders:

```bash
php artisan vendor:publish --tag=vuexy-admin-config
php artisan vendor:publish --tag=vuexy-admin-seeders
php artisan migrate --seed
```

Para publicar imágenes del tema:

```bash
php artisan vendor:publish --tag=vuexy-admin-images
```

---

## 🌍 Repositorio Principal y Sincronización

Este repositorio es una **copia sincronizada** del repositorio principal alojado en **[Tea - Koneko Git](https://git.koneko.mx/koneko/laravel-vuexy-admin)**.

### 🔄 Sincronización con GitHub
- **Repositorio Principal:** [git.koneko.mx](https://git.koneko.mx/koneko/laravel-vuexy-admin)
- **Repositorio en GitHub:** [github.com/koneko-mx/laravel-vuexy-admin](https://github.com/koneko-mx/laravel-vuexy-admin)
- **Los cambios pueden reflejarse primero en Tea antes de GitHub.**

### 🤝 Contribuciones
Si deseas contribuir:
1. Puedes abrir un **Issue** en [GitHub Issues](https://github.com/koneko-mx/laravel-vuexy-admin/issues).
2. Para Pull Requests, **preferimos contribuciones en Tea**. Contacta a `admin@koneko.mx` para solicitar acceso.

⚠️ **Nota:** Algunos cambios pueden tardar en reflejarse en GitHub, ya que este repositorio se actualiza automáticamente desde Tea.

---

## 🏅 Licencia

Este paquete es de código abierto bajo la licencia [MIT](LICENSE).

---

<p align="center">
    Hecho con ❤️ por <a href="https://koneko.mx">Koneko Soluciones Tecnológicas</a>
</p>
