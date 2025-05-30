# ⚙️ Laravel Vuexy Admin

<p align="center">
    <a href="https://koneko.mx" target="_blank">
        <img src="https://git.koneko.mx/Koneko-ST/koneko-st/raw/branch/main/logo-images/horizontal-05.png" width="400" alt="Koneko Soluciones Tecnológicas Logo">
    </a>
</p>

<p align="center">
    <a href="https://koneko.mx"><img src="https://img.shields.io/badge/Sitio%20Web-koneko.mx-blue" alt="Sitio Web"></a>
    <a href="https://packagist.org/packages/koneko/laravel-vuexy-admin"><img src="https://img.shields.io/packagist/v/koneko/laravel-vuexy-admin" alt="Versión estable"></a>
    <a href="https://packagist.org/packages/koneko/laravel-vuexy-admin"><img src="https://img.shields.io/packagist/l/koneko/laravel-vuexy-admin" alt="Licencia"></a>
    <a href="https://github.com/koneko-mx/laravel-vuexy-admin"><img src="https://img.shields.io/github/issues/koneko-mx/laravel-vuexy-admin" alt="Issues"></a>
</p>

---

## 📌 Descripción

**Laravel Vuexy Admin** es un componente central del ecosistema **Koneko ERP** desarrollado en Laravel 11, orientado a proyectos empresariales y administrativos para el mercado mexicano y latinoamericano. Está basado en el reconocido template premium **[Vuexy Admin Template](https://themeforest.net/item/vuexy-vuejs-html-laravel-admin-dashboard-template/23328599)** (de pago), el cual **debe adquirirse de forma legal** para usarse en proyectos de producción.

Este paquete facilita la implementación de interfaces administrativas modernas, responsivas y seguras, con un sistema modular escalable, permisos jerárquicos, roles, trazabilidad de acciones y soporte multilenguaje.

---

## ✨ Características

* ✅ Autenticación moderna con Laravel Fortify
* ✅ Panel de usuarios con Livewire 3
* ✅ Sistema de permisos basado en Spatie
* ✅ Auditoría completa con OwenIt Laravel Auditing
* ✅ Interfaz modular y administración intuitiva
* ✅ Compatibilidad total con Laravel 11
* ✅ Compatible con Redis y PostgreSQL
* ✅ Preparado para cacheo y entornos productivos

---

## 📦 Instalación

### Opcion 1: Desde Packagist (Recomendado)

```bash
composer require koneko/laravel-vuexy-admin
```

### Opcion 2: Desde repositorio Git (GitHub o Tea)

```json
"repositories": {
    "koneko/laravel-vuexy-admin": {
        "type": "vcs",
        "url": "https://github.com/koneko-mx/laravel-vuexy-admin"
    }
}
```

```bash
composer require koneko/laravel-vuexy-admin:@dev
```

---

## 🚀 Uso Básico

```php
use Koneko\VuexyAdmin\Models\User;

$user = User::create([
    'name' => 'Juan Pérez',
    'email' => 'juan@example.com',
    'password' => bcrypt('secret'),
]);
```

---

## 📚 Configuración y Publicación

```bash
php artisan vendor:publish --tag=vuexy-admin-config
php artisan vendor:publish --tag=vuexy-admin-seeders
php artisan migrate --seed
```

---

## 🌍 Consideraciones Legales

Este proyecto se basa parcialmente en el template **Vuexy Admin Template** de ThemeForest, el cual **no está incluido** en este repositorio. Si deseas utilizar este proyecto en producción, debes adquirir una licencia legal del template a través del siguiente enlace:

👉 [https://themeforest.net/item/vuexy-vuejs-html-laravel-admin-dashboard-template/23328599](https://themeforest.net/item/vuexy-vuejs-html-laravel-admin-dashboard-template/23328599)

Koneko Soluciones Tecnológicas no distribuye ni redistribuye archivos de dicho template. Este repositorio es un paquete Laravel desacoplado con soporte para integrarse con dicho sistema.

---

## 🤝 Contribuciones

Este proyecto es de código abierto bajo la licencia Business Source 1.1 con transición a MIT. Está abierto a contribuciones:

* Puedes comenzar revisando los issues etiquetados como `help wanted` o `good first issue`.
* Por ahora solo existe la rama `release/beta`, pero se aceptarán mejoras y PRs organizados.
* Todo el desarrollo principal se realiza en el servidor Git privado de Koneko (`git.koneko.mx`), pero el repositorio en GitHub está sincronizado y abierto para colaboraciones.

✉️ Para propuestas mayores o contacto profesional: `opensource@koneko.mx`

---

## 📚 Documentación y Comunidad

Este paquete forma parte del ecosistema **Koneko ERP Modular**, que incluye:

* `koneko/laravel-vuexy-admin` (este repo)
* `koneko/laravel-vuexy-website-admin`
* `koneko/laravel-vuexy-website-layout-porto`

Visita el sitio oficial: [https://koneko.mx](https://koneko.mx)

---

## 🛠️ Requisitos

* PHP `^8.2`
* Laravel `^11.31`
* Node + Vite para personalización con SCSS (opcional pero recomendado)

---

## 📄 Licencia

Este paquete se distribuye bajo la [Licencia Business Source 1.1 personalizada](LICENSE.es), con transición automática a MIT a los 3 años. Para uso comercial, redistribución o integraciones ampliadas, contacta a:

📧 [opensource@koneko.mx](mailto:opensource@koneko.mx)

---

## 📚 Más Información

* [Documentación en inglés](README.en.md)
* [Sitio Oficial Koneko ST](https://koneko.mx)
* [Correo de Contacto](mailto:opensource@koneko.mx)

---

<p align="center">
    Hecho con ❤️ en México por <a href="https://koneko.mx">Koneko Soluciones Tecnológicas</a>
</p>
