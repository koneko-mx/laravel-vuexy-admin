# Notificaciones en Koneko ERP

Este documento describe cómo utilizar el sistema de notificaciones centralizado mediante `NotifyChannelService` en el ecosistema Koneko ERP.

## 🧩 Drivers disponibles

- **Toastr** (predeterminado, clásico)
- **Notyf** (UX moderna, ligera)
- **Toastify** (notificaciones mínimas flotantes)
- **SweetAlert2** (modales avanzados, carga bajo demanda)
- **PNotify** (notificaciones avanzadas y persistentes, carga bajo demanda)
- **Custom** (ejecuta `window.VuexyNotify(payload)` si está definido)

## 📦 Uso básico

Puedes disparar una notificación global desde cualquier JS:

```js
import { NotifyChannelService } from '@/vuexy/notifications/notify-channel-service';

NotifyChannelService.notify({
  type: 'success',
  message: 'Usuario guardado correctamente',
  title: 'Éxito',
  channel: 'toastr' // 'notyf', 'toastify', 'sweetalert', 'pnotify', 'custom'
});
```

## 🌐 Desde Livewire

Puedes emitir un evento desde tu componente PHP:

```php
$this->dispatchBrowserEvent('notify', [
    'type' => 'success',
    'message' => 'Operación exitosa',
    'channel' => 'toastify'
]);
```

Y en tu JS global:

```js
document.addEventListener('notify', (event) => {
    window.NotifyChannelService.notify(event.detail);
});
```

## 📄 Recomendaciones

- **Toastr**, **Toastify** y **Notyf** pueden convivir cargados.
- **SweetAlert2** y **PNotify** se cargan dinámicamente.
- Configura el canal por defecto desde `settings('core.notify.driver')` si lo deseas.

