<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Enums\SystemNotifications;

enum SystemNotificationStyle: string
{
    case Toast  = 'toast';
    case Banner = 'banner';
    case Modal  = 'modal';
    case Inline = 'inline';
}
