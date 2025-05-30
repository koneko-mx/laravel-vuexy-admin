# 🤝 Guía para Contribuidores

¡Gracias por tu interés en colaborar con **Koneko ERP**!
Este proyecto busca ofrecer una suite profesional de administración web y ERP orientado al mercado mexicano y LATAM, bajo licencia **Business Source License 1.1** con transición futura a MIT.

---

## 📌 Requisitos básicos

* Conocimiento de **PHP 8.2+** y **Laravel 11**
* Respeto por las buenas prácticas, código limpio y trabajo en equipo
* Preferentemente, experiencia en:

  * Livewire
  * Spatie Permissions & Roles
  * Laravel Fortify y Sanctum
  * Desarrollo modular con Composer

---

## 🌱 Primeros pasos

1. **Forkea** este repositorio y clónalo localmente.

2. Instala las dependencias con Composer:

   ```bash
   composer install
   ```

3. Copia el archivo `.env.example` a `.env` y ajusta tus credenciales locales:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Ejecuta las migraciones y seeders (si aplica):

   ```bash
   php artisan migrate --seed
   ```

5. Inicia el servidor:

   ```bash
   php artisan serve
   ```

---

## 📀 Convenciones del Proyecto

Antes de contribuir, asegúrate de leer nuestras
📁 [Convenciones de Componentes](CONVENTIONS.md),
donde definimos estructura de carpetas, estilo de código y otras buenas prácticas clave para mantener la consistencia del ecosistema **Koneko ERP**.

---

## 📝 Código de Conducta

Consulta nuestro [Código de Conducta](CODE_OF_CONDUCT.md) para conocer las expectativas y reglas que garantizan un ambiente colaborativo, profesional y respetuoso.

---

## 🚀 ¡Listo para colaborar!

Una vez que tengas tu entorno configurado:

* Busca issues etiquetados como `good first issue` o `help wanted`
* Lee el archivo [`CONTRIBUTING.md`](CONTRIBUTING.md) del módulo específico si estás trabajando en un subpaquete
* No olvides seguir las convenciones de estilo PSR-12 y usar `php-cs-fixer` si lo tienes disponible

---

Gracias por ser parte de **Koneko ERP** 💙
¡Esperamos tu contribución!
