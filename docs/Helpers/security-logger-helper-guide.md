# Koneko Security Logger Helper Guide

> ⚖️ Auditoría de eventos de seguridad en tiempo real y bajo demanda.

El logger `log_security()` forma parte de la infraestructura central de seguridad de Koneko Vuexy Admin y te permite registrar eventos sensibles como accesos, fallos de login, intentos sospechosos, detecciones de VPN, entre otros.

---

## ✨ Ventajas

* No requiere instanciar clases.
* Autoobtiene `Request`, `IP`, `UserAgent`, `Geolocalización`, etc.
* Flexible para ejecutarse desde controladores, middleware, jobs o Livewire.
* Compatible con `SecurityEvent` (modelo auditado).
* Almacena contexto extendido con `payload`, `is_proxy`, `user_id`, etc.

---

## ✨ Uso básico

```php
log_security('login_failed');
```

Esto registra un fallo de login con todos los metadatos de seguridad: IP, ciudad, dispositivo, agente, URL y más.

---

## ⚖️ Sintaxis Completa

```php
log_security(
    string $type,                    // Tipo de evento (ej: login_failed, login_success)
    ?Request $request = null,        // Puede inyectarse manualmente
    ?int $userId = null,             // Usuario relacionado (opcional)
    array $payload = [],             // Datos adicionales como intentos, cabeceras, etc.
    bool $isProxy = false            // ¿Fue detectado uso de proxy/VPN?
);
```

Ejemplo completo:

```php
log_security(
    'login_failed',
    request(),
    $user?->id,
    ['intentos' => 3, 'via' => 'formulario'],
    detectaVpnProxy(request()->ip())
);
```

---

## 🔍 Eventos soportados por defecto

Puedes definirlos como constantes estáticas en tu código:

```php
SecurityEvent::EVENT_LOGIN_FAILED
SecurityEvent::EVENT_LOGIN_SUCCESS
```

O usar strings arbitrarios con sentido:

```php
'password_reset_attempt'
'blocked_login_from_blacklist'
'csrf_token_fail'
'geolocation_warning'
```

---

## ⌚ Modelo generado: `SecurityEvent`

El evento registrado se almacena en la tabla `security_events`, con campos como:

* `ip_address`
* `user_id`
* `event_type`
* `payload`
* `region`, `city`, `country`
* `is_proxy`, `device_type`, etc.

---

## 🌐 Geolocalización Automática

Si tienes habilitado el trait `HasGeolocation`, el sistema hace *GeoIP Lookup* por IP:

```php
use Koneko\VuexyAdmin\Support\Traits\Helpers\HasGeolocation;
```

---

## 🔐 Buenas prácticas

* Usa tipos semánticos: `login_success`, `vpn_blocked`, `csrf_fail`, etc.
* Agrega contexto extra en `payload` para debugging posterior.
* Detecta IPs sospechosas con `isProxy` (ideal para usar junto con sistemas de listas negras).

---

## ✨ Extras

Puedes extender la lógica de logging desde:

```php
Koneko\VuexyAdmin\Support\Logger\KonekoSecurityLogger
```

Este servicio puede adaptarse o sustituirse por otro para logs distribuidos o externos (ej: Sentry, Elastic, etc).

---

## ✅ Conclusión

El helper `log_security()` es una herramienta robusta, flexible y elegante para capturar eventos sensibles en tu ERP. Evita trabajar directamente con el modelo, mantén la consistencia de tu registro, y delega la trazabilidad a una capa preparada para el contexto empresarial.
