# Koneko System Logger Helper Guide

El sistema de logs de sistema en Koneko ERP permite registrar eventos importantes de forma estructurada, segura y extensible. A través del helper `log_system()` se abstrae la lógica de registro para facilitar su uso sin exponer la clase subyacente `KonekoSystemLogger`.

## ✅ ¿Cuándo usar `log_system()`?

Este helper debe utilizarse para registrar:

- Operaciones del sistema (módulos, configuración, instalación de paquetes)
- Procesos técnicos (errores, warnings, notificaciones internas)
- Eventos relevantes relacionados con lógica de negocio o flujos administrativos

---

## 📦 Helper: `log_system()`

```php
log_system(
    string|LogLevel $level,
    string $message,
    array $context = [],
    ?Model $related = null
): SystemLog
```

### Parámetros
| Nombre       | Tipo                            | Descripción |
|--------------|----------------------------------|-------------|
| `$level`     | `string\|LogLevel`              | Nivel del log (`info`, `warning`, `error`, `critical`, etc.) |
| `$message`   | `string`                         | Mensaje a registrar |
| `$context`   | `array`                          | Datos adicionales relevantes al evento |
| `$related`   | `Model|null`                     | Modelo relacionado (opcional, se guarda en `related_model`) |

---

## 🎯 Ejemplos de uso

### Log simple con nivel:
```php
log_system('info', 'Inicio del proceso de sincronización');
```

### Log con contexto personalizado:
```php
log_system('error', 'Error al procesar factura CFDI', [
    'cfdi_id' => 3345,
    'error' => $exception->getMessage(),
]);
```

### Log vinculado a un modelo:
```php
log_system(
    'warning',
    'El producto fue modificado manualmente',
    ['user' => auth()->id()],
    $product
);
```

---

## 🧱 Internamente...

- Se utiliza el modelo `Koneko\VuexyAdmin\Models\SystemLog`
- Soporta `morphTo()` para asociar cualquier modelo relacionado (via `related_model`)
- Se castea `level` como Enum `LogLevel`
- Se incluye automáticamente el componente si está registrado vía `KonekoComponentContextRegistrar`

---

## 🛡️ Buenas prácticas

- Usa niveles correctos (`info`, `debug`, `warning`, `error`, `critical`) según la gravedad
- Agrega siempre contexto útil que facilite auditoría
- Usa `related` cuando el evento está directamente vinculado a un modelo (como un `Pedido`, `Producto`, etc.)
- Si estás en un módulo registrado, el helper asocia automáticamente el `componentNamespace`

---

## 🔐 Soporte a auditoría

`log_system()` es parte fundamental del sistema de trazabilidad técnica del ERP. Todos los registros quedan disponibles para consulta por el módulo de Auditoría o Seguridad Avanzada si está habilitado.

---

## 📍 Registro automático de módulo

Si el componente actual fue registrado mediante:
```php
KonekoComponentContextRegistrar::registerComponent('admin');
```
El log quedará asociado a `module = 'admin'`, sin necesidad de especificarlo manualmente.

---

## 📚 Relación con otros loggers
| Helper              | Propósito                         |
|---------------------|-----------------------------------|
| `log_system()`      | Logs técnicos y operativos        |
| `log_security()`    | Eventos de seguridad (auth, IP)  |
| `log_interaction()` | Interacciones del usuario final   |

---

## 🧪 Testing y ambiente local

En `local` o `staging`, es común agregar logs temporales para diagnóstico:
```php
log_system('debug', 'Revisando flujo de pago', ['step' => 3]);
```

Recuerda que estos deben eliminarse o ajustarse a `info` en producción.

---

## 🧭 Ubicación del modelo
```php
Koneko\VuexyAdmin\Models\SystemLog
```

Puedes extender la funcionalidad desde el modelo si se requiere una visualización especial para auditoría o tablas administrativas.

---

> Este helper está diseñado para desarrolladores del ecosistema Koneko. Evita el uso directo de `KonekoSystemLogger` salvo en integraciones muy especializadas.
