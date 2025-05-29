# Koneko ERP - Cache Helper Guide

> ✨ Esta guía detalla el uso correcto del sistema de cache en Koneko ERP, basado completamente en el helper `cache_m()`, sin necesidad de interactuar con las clases internas como `KonekoCacheManager` o `LaravelCacheManager`.

---

## 🔎 Filosofía

* Toda interacción de componentes con el sistema de cache debe realizarse exclusivamente mediante el helper `cache_m()`.
* Las clases internas son consideradas **@internal**, y no deben ser accedidas directamente por desarrolladores de componentes.
* El sistema permite configuración jerárquica basada en namespace del componente, grupo lógico de datos y claves individuales.

---

## 🔍 Sintaxis del Helper

```php
cache_m(string $component = 'admin', string $group = 'cache')
```

Retorna una instancia segura del gestor para el componente y grupo indicados. Ejemplos:

```php
cache_m('admin', 'avatar')->enabled();
cache_m('website', 'menu')->ttl();
cache_m('website', 'html')->flush();
```

---

## ⚖️ Jerarquía de Configuración

Las operaciones de cache respetan la siguiente jerarquía al determinar configuraciones:

1. `koneko.{componente}.{grupo}.ttl` / `enabled`
2. `koneko.{componente}.cache.ttl` / `enabled`
3. `koneko.cache.ttl` / `enabled`

Esto permite granularidad sin perder coherencia global.

---

## 📃 Métodos Disponibles

| Método                   | Descripción                                                   |
| ------------------------ | ------------------------------------------------------------- |
| `key(string $suffix)`    | Genera clave de cache completa con prefijos                   |
| `config($key, $default)` | Accede a configuraciones con prefijo aplicado                 |
| `ttl()`                  | TTL efectivo (en segundos)                                    |
| `enabled()`              | Determina si el cache está habilitado para el contexto actual |
| `flush()`                | Limpia el grupo de cache completo si el driver lo permite     |
| `driver()`               | Devuelve el driver de cache actual                            |
| `shouldDebug()`          | Evalúa si se encuentra en modo debug para cache               |
| `registerDefaults()`     | Registra valores default para ttl y enabled si no existen     |

---

## 🚀 Ejemplos de Uso

### Validar TTL efectivo

```php
$ttl = cache_m('website', 'menu')->ttl();
```

### Verificar si está habilitado

```php
if (cache_m('admin', 'avatar')->enabled()) {
    // Proceder con cache
}
```

### Limpiar cache con soporte para etiquetas

```php
cache_m('website', 'html')->flush();
```

---

## ⚠️ Buenas Prácticas

* **No** uses directamente la clase `KonekoCacheManager`.
* **No** accedas directamente al sistema `Cache::` sin pasar por el helper.
* **No** asumas estructura de clave, usa `->key()`.
* **Sí** configura `koneko.{componente}.{grupo}.ttl` en tu archivo `config()` si tu componente lo necesita.

---

## 🔍 Debug y Diagnóstico

Ejecuta:

```bash
php artisan koneko:cache --component=website --group=html --ttl
php artisan koneko:cache --component=admin --group=avatar --flush
```

El comando mostrará información relevante para depuración sin exponer clases internas.

---

## 🚧 Futura Expansión

* Registro de tags
* TTLs dinámicos
* Integración con eventos y auditoría
* Modo "read-only" para ambientes cacheados

---

## 🌟 Conclusión

Este sistema garantiza modularidad, extensibilidad y seguridad. El helper `cache_m()` es la única puerta de entrada para desarrolladores y debe usarse exclusivamente para mantener la integridad del ecosistema.

> ✅ Si necesitas agregar un nuevo grupo de cache, simplemente define su configuración y comienza a usar el helper, sin necesidad de modificar clases o contratos.
