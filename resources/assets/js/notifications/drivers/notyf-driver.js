import { Notyf } from 'notyf';
import 'notyf/notyf.min.css';

const notyf = new Notyf({
    duration: 5000,
    ripple: true,
    position: { x: 'right', y: 'top' },
});

export const NotyfDriver = {
    notify({ type = 'success', message = '' }) {
        notyf.open({
            type,
            message,
        });
    },
};