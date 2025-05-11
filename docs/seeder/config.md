# ⚙️ Configuración del sistema de seeders

El archivo `config/seeder.php` permite controlar el comportamiento global de todos los módulos.

## 🔑 Opciones principales

| Opción         | Tipo    | Descripción |
|----------------|---------|-------------|
| `env`          | string  | Entorno actual (local, demo, etc.) |
| `modules`      | array   | Lista de módulos con su configuración individual |
| `defaults`     | array   | Valores por defecto para todos los seeders |
| `clear_assets` | array   | Define qué carpetas se borran antes de iniciar |

## 🧩 Configuración por módulo

Cada módulo acepta:

```php
'users' => [
  'enabled' => true,
  'seeder' => UserSeeder::class,
  'file' => 'users.json',
  'depends_on' => ['permissions'],
  'truncate' => true,
  'faker_only' => false,
  'fake' => [
    'min' => 5,
    'max' => 100,
    'images' => [...]
  ],
],
```