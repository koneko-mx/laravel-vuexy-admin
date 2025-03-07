import '../../assets/js/notifications/LivewireNotification.js';
import SmtpSettingsForm from '../../js/smtp-settings/SmtpSettingsForm';
import SenderResponseForm from '../../js/smtp-settings/SenderResponseForm.js';

window.smtpSettingsForm = new SmtpSettingsForm();
window.senderResponseForm = new SenderResponseForm();

Livewire.hook('morphed', ({ component }) => {
    switch (component.name) {
        case 'mail-smtp-settings':
            if (window.smtpSettingsForm) {
                window.smtpSettingsForm.reload(); // Recarga el formulario sin destruir la instancia
            }
            break;

        case 'mail-sender-response-settings':
            if (window.senderResponseForm) {
                window.senderResponseForm.reload(); // Recarga el formulario sin destruir la instancia
            }
            break;

        default:
            break;
    }
});
