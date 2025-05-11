# 🌱 Koneko ERP - Sistema de Seeders Modular

Este sistema permite poblar la base de datos del ERP de forma controlada, desde archivos CSV/JSON o mediante generación con Faker, incluyendo soporte para:

- Seeders con dependencias (`depends_on`)
- Modo `dry-run` (simulación sin afectar BD)
- Reportes en formato Markdown y JSON
- Limpieza automática de assets (ej. avatares)
- Soporte para múltiples entornos (`SEEDER_ENV`)
- Generación de imágenes y datos falsos