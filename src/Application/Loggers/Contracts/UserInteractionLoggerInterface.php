<?php

namespace Koneko\VuexyAdmin\Application\Contracts\Loggers;

use Koneko\VuexyAdmin\Application\Enums\UserInteractions\InteractionSecurityLevel;
use Koneko\VuexyAdmin\Models\UserInteraction;

interface UserInteractionLoggerInterface
{
    public function record(
        string $action,
        array $context = [],
        InteractionSecurityLevel|string $security = 'normal',
        ?string $livewireComponent = null
    ): ?UserInteraction;
}
