# ![Koneko ERP](https://git.koneko.mx/koneko-st/koneko-st/raw/branch/main/logo-images/horizontal-05.png) Convenciones de Estructura de Componentes

📅 *Última actualización:* 2025-04-03
🔧 *Aplicable a todos los módulos Composer de Koneko ERP*

---

## 📁 Estructura General de un Componente

```plaintext
component-root/
├── config/                 ← Configuraciones del módulo
├── Database/
│   ├── data/               ← Archivos JSON, CSV, XLSX
│   ├── factories/          ← Factories para testing y seeders
│   ├── migrations/         ← Migraciones del esquema del módulo
│   └── Seeders/            ← Seeders base y de datos fake
├── Enums/                  ← Enums (PSR-4) usados por el módulo
├── Events/                 ← Eventos del módulo
├── Http/
│   ├── Controllers/        ← Controladores
│   └── Middleware/         ← Middlewares específicos del módulo
├── Livewire/               ← Componentes Livewire organizados por dominio
├── Models/                 ← Modelos Eloquent
├── Notifications/          ← Notificaciones personalizadas
├── Providers/              ← Service Providers del módulo
├── Services/               ← Servicios (lógica de negocio)
├── Support/
│   ├── Base/               ← Clases base abstractas
│   ├── Builders/           ← Configuradores de vistas tipo índice
│   ├── Macros/             ← Macros de Str, Collection, etc.
│   ├── Queries/            ← Query Builders avanzados
│   ├── Registries/         ← Registro dinámico de configuración
│   └── Validation/         ← Validaciones personalizadas
├── Traits/
│   ├── Audit/              ← Traits para auditoría y tracking
│   ├── Metadata/           ← Traits para metadatos del modelo
│   ├── Users/              ← Traits relacionados con usuarios
│   └── Indexing/           ← Traits usados por configuradores de índices
├── resources/
│   ├── assets/             ← JS, SCSS, íconos o fuentes específicos
│   ├── faker-images/       ← Imágenes utilizadas en datos de prueba
│   ├── lang/               ← Archivos de traducción
│   └── views/              ← Vistas Blade
├── routes/
│   └── admin.php           ← Rutas internas del módulo
├── storage/                ← Recursos adicionales (ej. fuentes)
└── README.md               ← Documentación del componente
```

---

## 🧠 Convenciones Generales

- Todos los módulos deben seguir PSR-4.
- Los archivos deben nombrarse en *PascalCase* excepto `config/*.php` y rutas.
- Los `Seeder` deben ser agrupados por módulo si el componente los agrupa (ej. `vuexyAdmin`, `vuexyWarehouse`).
- Los `Factory` deben ser compatibles con `SeederWithFakeImages`.

---

## 🖼️ Imágenes Faker

- Carpeta: `resources/faker-images/<dominio>`
- Subcarpetas válidas: `users/`, `stores/`, `products/`, etc.
- Las imágenes se usan exclusivamente para entornos de testing/demostración.
- Nunca se publican al frontend ni se exponen directamente.

---

## 🧪 Factories

- Todas las `factories` deben estar en `Database/factories/`.
- Si se extiende un modelo (`Koneko\VuexyAdmin\Models\User`), usar `new (User::class)` dinámico.
- Compatible con `SeederOrchestrator` y `config/seeder.php`.

---

## 📊 Configuradores de Índice

- Los index deben implementar `BaseModelIndexConfig` o su extensión.
- Pueden usar Traits como `HandlesFactory`, `HandlesIndexColumns`, `HandlesQueryBuilder`, etc.
- Se recomienda usar `Support/Builders/` para los configuradores y `Support/Registries/` si son extendibles.

---

## 📚 Traducciones

- Usar `resources/lang/es/` con archivos separados por dominio (`auth.php`, `validation.php`, etc.).
- `es_MX.json` puede usarse para traducciones inline.

---

## 📌 Tips

- Si un componente tiene `Service`, `Seeder`, `Factory` y `Livewire`, deben estar todos organizados en sus carpetas respectivas.
- La estructura del componente debe ser lo suficientemente clara para no depender de documentación externa.

---

## 🤝 ¿Dudas o sugerencias?

Este documento está en constante mejora. Si tienes sugerencias o deseas proponer mejoras, por favor abre un [issue de tipo mejora](.github/ISSUE_TEMPLATE/feature_request.md) o contribuye con un Pull Request.

---
