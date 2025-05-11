export const SweetAlertDriver = {
    async notify(options = {}) {
        const Swal = (await import('sweetalert2')).default;

        Swal.fire({
            icon: options.type || 'info',
            title: options.title || '',
            text: options.message || '',
            timer: options.delay || 5000,
            timerProgressBar: true,
            showConfirmButton: false,
            ...options,
        });
    },
};