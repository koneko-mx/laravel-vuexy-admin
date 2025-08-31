<?php

namespace Koneko\VuexyAdmin\Application\Settings\Concerns;

trait HasSettingMetadata
{
    protected array $metadata = [
        'description' => null,
        'hint'        => null,
    ];

    public function description(string $description): static
    {
        $this->metadata['description'] = $description;
        return $this;
    }

    public function hint(string $hint): static
    {
        $this->metadata['hint'] = $hint;
        return $this;
    }
}
