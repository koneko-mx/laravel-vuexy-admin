<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Traits\Helpers;

trait HasGeolocation
{
    public function getCoordinates(): ?array
    {
        return ($this->lat && $this->lng) ? [$this->lat, $this->lng] : null;
    }
}
