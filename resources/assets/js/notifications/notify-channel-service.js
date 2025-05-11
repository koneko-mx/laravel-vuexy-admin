// notify-channel-service.js
import { VuexyNotifyService } from './vuexy/vuexy-notify';
import { ToastifyDriver } from './drivers/toastify-driver';
import { NotyfDriver } from './drivers/notyf-driver';
import { ToastrDriver } from './drivers/toastr-driver';
import { VuexyDriver } from './drivers/vuexy-driver';

const NotifyChannelService = {
    async notify({ channel = 'toastr', ...payload }) {
        switch (channel) {
            case 'toastr':
                ToastrDriver.notify(payload);
                break;

            case 'notyf':
                NotyfDriver.notify(payload);
                break;

            case 'toastify':
                ToastifyDriver.notify(payload);
                break;

            case 'sweetalert': {
                const { SweetAlertDriver } = await import('./drivers/sweetalert-driver');
                SweetAlertDriver.notify(payload);
                break;
            }

            case 'vuexy':
                VuexyDriver.notify(payload);
                break;

            case 'custom':
                if (typeof window.VuexyNotify === 'function') {
                    window.VuexyNotify(payload);
                }
                break;

            default:
                console.warn(`[Notify] Canal desconocido: ${channel}`);
                break;
        }
    },
    fromLocalStorage() {
        const data = localStorage.getItem('vuexy_notification');

        if (data) {
            try {
                const payload = JSON.parse(data);
                NotifyChannelService.notify(payload);
                localStorage.removeItem('vuexy_notification');
            } catch (e) {
                console.error('❌ Error parseando notificación de storage', e);
            }
        }
    },
    fromSession() {
        if (window.vuexySessionNotification) {
            NotifyChannelService.notify(window.vuexySessionNotification);
            window.vuexySessionNotification = null;
        }
    }
};

export { NotifyChannelService };

window.vuexyNotify = VuexyNotifyService;
