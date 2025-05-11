import Toastify from 'toastify-js';
import 'toastify-js/src/toastify.css';

export const ToastifyDriver = {
    notify({ message = '', type = 'default', duration = 4000, position = 'right' }) {
        Toastify({
            text: message,
            duration: duration,
            gravity: 'top',
            position: position,
            className: `toastify-${type}`,
            close: true,
        }).showToast();
    },
};
