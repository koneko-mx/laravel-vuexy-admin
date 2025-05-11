# Guía de Convenciones para `database/data/` en Koneko Vuexy Admin/ERP

> Esta guía está diseñada para ayudar a desarrolladores a organizar, entender y aprovechar correctamente la estructura de carpetas `database/data/*` en los módulos del ecosistema **Koneko Vuexy Admin/ERP**. Aplica tanto para datos de prueba como para catálogos oficiales, importaciones, plantillas y volcados.

---

## 📂 Estructura base sugerida

Cada componente o módulo deberá tener su propia carpeta bajo `database/data/`, utilizando el nombre del paquete o alias del módulo. Por ejemplo:

```bash
database/data/vuexy-contacts/
database/data/vuexy-sat-catalogs/
database/data/vuexy-ecommerce/
database/data/vuexy-core/
```

---

## 📄 Tipos de carpetas internas

A continuación se detallan los tipos de subcarpetas recomendadas dentro de cada componente:

### 1. `seeders/`

Contiene archivos de datos utilizados para poblar la base de datos con registros reales o iniciales.

* Formatos: `.json`, `.csv`, `.php`
* Uso: Comandos como `php artisan db:seed` o `SeederOrchestrator`

**Ejemplos:**

```bash
database/data/vuexy-contacts/seeders/users.json
database/data/vuexy-ecommerce/seeders/products.csv
```

---

### 2. `fixtures/`

Datos temporales o de prueba. Generalmente usados para QA, testeo, demostraciones o sandbox.

**Ejemplos:**

```bash
database/data/vuexy-ecommerce/fixtures/demo-products.csv
database/data/vuexy-core/fixtures/test-users.json
```

---

### 3. `catalogs/`

Incluye catálogos oficiales o externos (como los del SAT, INEGI, ISO, etc.) ya convertidos a `.csv` o `.json`, listos para importar.

**Ejemplos:**

```bash
database/data/vuexy-sat-catalogs/catalogs/c_RegimenFiscal.csv
database/data/vuexy-sat-catalogs/catalogs/c_UsoCFDI.csv
```

---

### 4. `samples/` o `templates/`

Plantillas de importación para administradores o usuarios finales. Ayudan a estructurar correctamente los datos antes de importar.

**Ejemplos:**

```bash
database/data/vuexy-website-admin/samples/template_users.csv
database/data/vuexy-ecommerce/samples/sample_product.json
```

---

### 5. `dumps/`

Volcados de base de datos completos o parciales, usados como backups, sincronización o testing. No deben usarse en producción directamente.

**Ejemplos:**

```bash
database/data/vuexy-core/dumps/db-export.json
database/data/vuexy-ecommerce/dumps/partial-products.sql
```

---

### 6. `sources/`

Archivos fuente originales que requieren procesamiento, como `.xlsx` del SAT o archivos crudos.

**Ejemplos:**

```bash
database/data/vuexy-sat-catalogs/sources/SAT_Catalogos_2024.xlsx
database/data/vuexy-geo/sources/inegi_cp_2020.xlsx
```

---

### 7. `mappings/`

Mapas de conversión, reglas de transformación, diccionarios de campos, etc. Muy útiles cuando se procesan archivos externos.

**Ejemplos:**

```bash
database/data/vuexy-sat-catalogs/mappings/column_map.json
database/data/vuexy-ecommerce/mappings/type_rules.json
```

---

### 8. `commands/` (opcional)

Scripts de ejemplo para parseo, importación o generación de datos. No se ejecutan automáticamente, son de referencia.

**Ejemplo:**

```bash
database/data/vuexy-sat-catalogs/commands/parse_sat_catalog.php
```

---

## 📊 Publicación de archivos desde Providers

Los `ServiceProvider` de cada módulo pueden publicar estos archivos para que el desarrollador los copie al proyecto:

```php
'publishedFiles' => [
    'seeders' => [
        'database/data/vuexy-contacts/seeders' => base_path('database/data/vuexy-contacts/seeders'),
    ],
    'catalogs' => [
        'database/data/vuexy-sat-catalogs/catalogs' => base_path('database/data/vuexy-sat-catalogs/catalogs'),
    ],
],
```

---

## ✨ Buenas prácticas

* Usa nombres descriptivos: `products_v2.json`, `c_UsoCFDI_2024.csv`
* No sobreescribas datos productivos desde estas carpetas.
* Prefiere `json` para estructuras complejas, `csv` para tabulares simples.
* Siempre separa datos de prueba (`fixtures`) de datos de producción (`seeders`).
* Versiona si es necesario: `v1/`, `v2/`.

---

## ❓ FAQ

**¿Puedo usar archivos de `samples/` como plantilla para importadores Livewire?**

> Sí, están pensados como guías para el usuario final.

**¿Debo publicar todos los archivos por defecto?**

> No. Publica solo los que consideres necesarios para instalación o personalización.

**¿Puedo incluir múltiples carpetas del mismo tipo?**

> Sí. Puedes tener `catalogs/v3/` y `catalogs/v4/` si soportas distintas versiones.

---

## 🚀 Extensiones futuras

* Agregar validación automática de estructuras CSV.
* Sistema de visualización para cada tipo de archivo desde el admin.
* Comandos de limpieza automática (`vuexy:data:cleanup`)

---

¡Esta convención mejora la modularidad, facilita el mantenimiento y hace tu ERP más robusto y predecible!
