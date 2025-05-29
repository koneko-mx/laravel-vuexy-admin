<?php

namespace Koneko\VuexyAdmin\Support\Enums\SystemNotifications;

enum SystemNotificationType: string
{
    case Info    = 'info';
    case Success = 'success';
    case Warning = 'warning';
    case Danger  = 'danger';
    case Promo   = 'promo';
}