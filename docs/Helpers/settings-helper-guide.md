# Koneko Settings Helper Guide

Guía oficial de uso para `settings()` dentro del ecosistema Koneko ERP.

---

## 📁 Filosofía General

KonekoSettingManager es una clase interna (@internal) encargada de gestionar configuraciones de componentes y módulos del ecosistema de forma centralizada, modular y cacheable. No debe ser usada directamente.

### Acceso siempre mediante el helper global:

```php
settings()->get('clave');
settings()->set('clave', 'valor');
```

---

## ⚖️ Jerarquía de Namespace (Prioridad de Resolución)

```text
componentNamespace (ej: admin, website)
    └️ module_slug (ej: avatar, seo, menu)
        └️ key
```

Ejemplo real:

```text
koneko.admin.avatar.cache.ttl
```

---

## ✨ Métodos Disponibles

### `self(?string $subNamespace = null)`

Se vincula automáticamente al componente activo según el contexto del módulo actual.

```php
settings()->self()->get('cache.ttl');
```

### `in(string $namespace)`

Permite establecer un namespace manual.

```php
settings()->in('admin.avatar')->get('enabled');
```

### `get(string $key, ...$args)`

Obtiene el valor de un setting.

```php
$ttl = settings()->get('cache.ttl');
```

### `set(string $key, mixed $value, ?int $userId = null)`

Guarda o actualiza un setting.

```php
settings()->set('menu.visible', true);
```

### `currentNamespace()`

Devuelve el namespace actual del contexto.

### `listGroups()`

Devuelve una colección de todos los grupos de configuración disponibles en el componente.

---

## 🚀 Comportamiento Avanzado

### ❄️ Cache Automática

* Al consultar un setting se usa el `KonekoCacheManager` de fondo si está habilitado.
* Puedes controlar el TTL y activación por:

```php
koneko.cache.enabled
koneko.admin.cache.ttl
koneko.admin.avatar.cache.enabled
```

### ⚖️ Tipos automáticos

* Si el valor del setting es JSON, se decodifica automáticamente.

### 🔨 Almacenamiento estructurado

Los datos se almacenan en la base de datos en la tabla `settings`:

* `namespace`
* `key`
* `value`
* `user_id`

---

## 🔧 Casos de Uso Comunes

```php
// 1. TTL de avatar (modo simple)
$ttl = settings()->in('admin.avatar')->get('cache.ttl');

// 2. Cambiar visibilidad del menú del website
settings()->in('website.menu')->set('visible', true);

// 3. Desde un componente que ya registró su namespace:
settings()->get('enabled');

// 4. En test con namespace explícito:
settings()->in('admin.seo')->set('json_ld.enabled', true);
```

---

## ⚡ Recomendaciones

* Nunca accedas directamente a `KonekoSettingManager`
* Usa `settings()` siempre que necesites configuración
* El namespace se debe registrar automáticamente con `KonekoComponentContextRegistrar`
* Puedes definir valores por `.env` o `config()` que serán sobreescritos si existen en la base de datos

---

## 🚩 Diagnóstico Rápido

```php
settings()->in('website.menu')->get('debug');
settings()->in('admin.avatar')->currentNamespace();
```

---

## ✨ Integración UI

Todos los valores son consultables y modificables desde:

* Panel de administrador
* Vistas Livewire
* Componentes Vue o Blade mediante API

---

## 🙏 Agradecimientos

Sistema inspirado por la necesidad de centralizar configuraciones por módulo en entornos escalables y cacheables. Diseñado con amor para el ERP Koneko Vuexy Admin México.

---

\#❤️ ¿Aportaciones?
Si tienes sugerencias, no dudes en compartirlas en el repositorio oficial Koneko.
