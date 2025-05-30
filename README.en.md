# 🎨 Laravel Vuexy Admin

<p align="center">
    <a href="https://koneko.mx" target="_blank">
        <img src="https://git.koneko.mx/Koneko-ST/koneko-st/raw/branch/main/logo-images/horizontal-05.png" width="400" alt="Koneko Soluciones Tecnológicas Logo">
    </a>
</p>

<p align="center">
    <a href="https://koneko.mx"><img src="https://img.shields.io/badge/Website-koneko.mx-blue" alt="Website"></a>
    <a href="https://packagist.org/packages/koneko/laravel-vuexy-admin"><img src="https://img.shields.io/packagist/v/koneko/laravel-vuexy-admin" alt="Stable Version"></a>
    <a href="https://packagist.org/packages/koneko/laravel-vuexy-admin"><img src="https://img.shields.io/packagist/l/koneko/laravel-vuexy-admin" alt="License"></a>
    <a href="https://github.com/koneko-mx/laravel-vuexy-admin"><img src="https://img.shields.io/github/issues/koneko-mx/laravel-vuexy-admin" alt="Issues"></a>
</p>

---

## 📌 Description

**Laravel Vuexy Admin** is a core component of the **Koneko ERP** ecosystem, built with Laravel 11 and tailored for enterprise and administrative applications in the Mexican and Latin American markets. It is based on the renowned premium template **[Vuexy Admin Template](https://themeforest.net/item/vuexy-vuejs-html-laravel-admin-dashboard-template/23328599)** (paid), which **must be legally purchased** to be used in production projects.

This package facilitates the implementation of modern, responsive, and secure admin interfaces with a scalable modular system, hierarchical permissions, roles, action auditing, and multilingual support.

---

## ✨ Features

* ✅ Modern authentication using Laravel Fortify
* ✅ User panel powered by Livewire 3
* ✅ Permissions system based on Spatie
* ✅ Full audit logging with OwenIt Laravel Auditing
* ✅ Modular interface and intuitive admin panel
* ✅ Full compatibility with Laravel 11
* ✅ Redis and PostgreSQL ready
* ✅ Cache-ready and production-oriented

---

## 📦 Installation

### Option 1: Via Packagist (Recommended)

```bash
composer require koneko/laravel-vuexy-admin
```

### Option 2: Via Git repository (GitHub or Tea)

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

## 🚀 Basic Usage

```php
use Koneko\VuexyAdmin\Models\User;

$user = User::create([
    'name' => 'Juan Pérez',
    'email' => 'juan@example.com',
    'password' => bcrypt('secret'),
]);
```

---

## 📚 Configuration & Publishing

```bash
php artisan vendor:publish --tag=vuexy-admin-config
php artisan vendor:publish --tag=vuexy-admin-seeders
php artisan migrate --seed
```

---

## 🌍 Legal Considerations

This project is partially based on the **Vuexy Admin Template** from ThemeForest, which is **not included** in this repository. If you want to use this project in production, you must purchase a legal license of the template at:

👉 [https://themeforest.net/item/vuexy-vuejs-html-laravel-admin-dashboard-template/23328599](https://themeforest.net/item/vuexy-vuejs-html-laravel-admin-dashboard-template/23328599)

Koneko Soluciones Tecnológicas does not distribute or redistribute files from this template. This repository is a Laravel package decoupled and prepared to integrate with such a system.

---

## 🤝 Contributions

This project is open-source under a custom Business Source License 1.1 transitioning to MIT. Contributions are welcome:

* Start by reviewing issues tagged as `help wanted` or `good first issue`.
* Currently, only the `release/beta` branch exists, but organized PRs are accepted.
* Core development occurs in our private Git server (`git.koneko.mx`), but the GitHub mirror is open for community collaboration.

✉️ For major proposals or professional contact: `opensource@koneko.mx`

---

## 📚 Documentation & Community

This package is part of the **Koneko Modular ERP** ecosystem, including:

* `koneko/laravel-vuexy-admin` (this repo)
* `koneko/laravel-vuexy-website-admin`
* `koneko/laravel-vuexy-website-layout-porto`

Visit our official site: [https://koneko.mx](https://koneko.mx)

---

## 🏅 License

This package is licensed under the [custom Business Source License 1.1](LICENSE), transitioning to MIT after 3 years.

---

## 🌎 More Information

* [Documentation in Spanish](README.es.md)
* [Koneko Official Website](https://koneko.mx)
* [Contact Email](mailto:opensource@koneko.mx)

<p align="center">
    Made with ❤️ in Mexico by <a href="https://koneko.mx">Koneko Soluciones Tecnológicas</a>
</p>
