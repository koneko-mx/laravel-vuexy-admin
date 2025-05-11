// resources/assets/js/notify-loader.js
import { NotifyChannelService } from './notify-channel-service.js';

document.addEventListener('DOMContentLoaded', () => {
    NotifyChannelService.fromLocalStorage();
    NotifyChannelService.fromSession();
});
