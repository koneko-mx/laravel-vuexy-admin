# Koneko ERP - Component Access & Exposure Policy

Este documento define las reglas de exposición y acceso para los servicios técnicos clave dentro del ecosistema **Koneko ERP**. Establece los principios para mantener una arquitectura limpia, segura y coherente, facilitando su extensión y documentación.

---

## ✅ Componentes y su Exposición

| Clase Técnica                     | Expuesta al Usuario | Acceso Recomendado         | Notas                                                            |
| --------------------------------- | ------------------- | -------------------------- | ---------------------------------------------------------------- |
| `KonekoSettingManager`           | ❌ No                | `settings()` helper        | Configuración modular con soporte de namespaces.                 |
| `KonekoCacheManager`              | ❌ No                | `cache_manager()` helper   | Acceso al sistema de cache multi-driver y con TTL configurables. |
| `KonekoSystemLogger`              | ❌ No                | `log_system()` helper      | Logger morfable con niveles y contexto extendido.                |
| `KonekoSecurityLogger`            | ❌ No                | `log_security()` helper    | Eventos de seguridad con GeoIP y proxy detection.                |
| `KonekoUserInteractionLogger`     | ❌ No                | `log_interaction()` helper | Auditoría de componentes y acciones sensibles.                   |
| `KonekoComponentContextRegistrar` | ❌ No                | ❄ Solo Bootstrap           | Registra el `namespace` del componente y su `slug` actual.       |

---

## ⚖️ Principios de Acceso

* **Acceso exclusivo por helpers**: Los helpers representan la única interfaz pública soportada para operaciones de configuración, cache y logs.
* **Encapsulamiento interno**: Ningún componente debería invocar directamente servicios como `KonekoSettingManager` o `KonekoSystemLogger`.
* **UI y CLI desacoplados**: Tanto los comandos artisan como el panel administrativo utilizan los helpers, nunca instancias directas.

---

## 💡 Buenas prácticas para desarrolladores

* Usa `settings()` para acceder o escribir configuraciones modulares.
* Usa `cache_manager()` para obtener TTL, flush o debug por componente.
* Usa `log_system()` para registrar eventos de sistema de forma morfable.
* Usa `log_security()` para eventos como logins fallidos o IP sospechosas.
* Usa `log_interaction()` para acciones en Livewire, eventos UI o tracking avanzado.

---

## 🔹 Ejemplo práctico

```php
// Correcto
settings()->in('website')->get('general.site_name');
cache_manager('admin', 'menu')->ttl();
log_system('info', 'Menú regenerado');
```

```php
// Incorrecto ❌
app(KonekoSettingManager::class)->get(...);
new KonekoCacheManager(...);
(new KonekoSystemLogger)->log(...);
```

---

## 📃 Futuras extensiones

* Se agregará soporte en este esquema para:

  * APIManager: `api_manager()`
  * Catalogs: `catalog()`
  * Event Dispatchers: `event_dispatcher()`

Cada uno seguirá el mismo patrón: clase técnica interna, helper de acceso documentado, y uso controlado por contexto registrado.

---

> Este documento forma parte del conjunto `docs/architecture/components/`. Asegúrese de mantenerlo actualizado ante cualquier refactor o nuevo helper público.
