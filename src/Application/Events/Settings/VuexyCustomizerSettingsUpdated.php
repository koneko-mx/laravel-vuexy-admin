<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Events\Settings;

class VuexyCustomizerSettingsUpdated
{
    public function __construct(public array $settings = []) {}
}
