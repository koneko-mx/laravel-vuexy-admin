# 🚀 Cómo ejecutar los seeders

## 🧪 Modo normal

```bash
php artisan migrate:fresh --seed
```

## 🎯 Ejecutar módulo específico

```bash
php artisan db:seed --class=DatabaseSeeder --only=users
```

## 🧹 Activar limpieza de assets

Controlado por `config/seeder.php > clear_assets`.

## 🧼 Dry run

```php
$orchestrator->run(only: 'users', options: ['dry-run' => true]);
```

## 📁 Reportes

Se generan automáticamente:

- `database/seeders/reports/local/seed-report-*.md`
- `database/seeders/reports/local/seed-report-*.json`