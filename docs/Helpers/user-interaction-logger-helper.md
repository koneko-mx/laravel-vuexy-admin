# Koneko User Interaction Logger Helper Guide

El sistema de interacciones de usuario de Koneko permite registrar acciones relevantes dentro de la interfaz de usuario (UI) o backend, con fines de auditoría, trazabilidad, y seguridad.

> Este mecanismo está diseñado para ser invocado desde componentes Livewire, controladores o procesos sensibles que desees monitorear.

---

### ✅ Uso recomendado

Usa el helper `log_interaction()` para registrar cualquier acción de interacción importante:

```php
log_interaction('update_profile', [
    'changes' => $changes,
]);
```

Puedes especificar el nivel de seguridad de la acción:

```php
log_interaction('delete_user', [
    'user_id' => $user->id,
], 'high');
```

Si estás dentro de Livewire:

```php
log_interaction('submit_form', [], 'normal', 'components.users.create-user');
```

---

### 💡 Sintaxis completa

```php
log_interaction(
    string $action,
    array $context = [],
    InteractionSecurityLevel|string $security = 'normal',
    ?string $livewireComponent = null
): ?UserInteraction
```

| Parámetro           | Descripción                                                                 |
|---------------------|------------------------------------------------------------------------------|
| `$action`           | Nombre de la acción realizada, ejemplo: `create_order`, `login_success`     |
| `$context`          | Arreglo con datos adicionales relevantes. Puede incluir cambios, payloads...|
| `$security`         | Nivel de seguridad: `low`, `normal`, `high`, `critical`                      |
| `$livewireComponent`| Ruta del componente Livewire, si aplica                                       |

---

### 🔍 Ejemplo realista

```php
log_interaction(
    action: 'update_user_permissions',
    context: [
        'user_id' => $user->id,
        'new_roles' => $roles,
    ],
    security: 'high',
    livewireComponent: 'components.admin.users.user-edit-panel'
);
```

---

### 📃 Sobre el modelo `UserInteraction`

No necesitas instanciar ni importar la clase `UserInteraction` para registrar interacciones. Sin embargo, si deseas auditar desde base de datos o hacer queries personalizados, puedes usar scopes como:

```php
UserInteraction::byModule('admin')->latest()->get();
```

---

### 🔧 Extendibilidad

Puedes extender `KonekoUserInteractionLogger` para registrar interacciones con fuentes externas, agregar metadata, o enviar notificaciones.

---

### ❌ Buenas prácticas a evitar

- No usar `UserInteraction::create(...)` manualmente.
- No cambiar campos protegidos (`action`, `security_level`, etc.) después del registro.
- No registrar acciones irrelevantes o sin contexto claro.

---

### 📆 Logs relacionados

- `log_system()` — para eventos del sistema.
- `log_security()` — para eventos de seguridad como accesos o bloqueos.

---

### 🚀 Módulos compatibles

Este sistema está disponible para todos los componentes que registren `componentNamespace` en su `KonekoModule.php`, permitiendo trazabilidad completa por módulo.

---

### 🌟 Tip final

Agrégalo a tus componentes Livewire clave para auditar formularios, acciones de administración, y cambios críticos del sistema.
