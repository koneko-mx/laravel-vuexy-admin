<?php

namespace Koneko\VuexyAdmin\Application\UX\Notifications\Support;

class NotifyPayload
{
    public string $scope;
    public string $title;
    public string $body;

    public string $type = 'info';               // success, warning, error, info
    public string $channel = 'toast';           // toast, modal, banner, etc.
    public ?string $target = null;              // div destino, modal id, etc.

    public ?int $timeout = null;                // tiempo en milisegundos
    public bool $persist = false;               // si se guarda en inbox/db
    public bool $requiresConfirmation = false;  // si requiere confirmación del usuario

    public array $data = [];                    // payload extra para JS o logs

    public static function make(array $data): static
    {
        $instance = new static();

        foreach ($data as $key => $value) {
            if (property_exists($instance, $key)) {
                $instance->{$key} = $value;
            }
        }

        return $instance;
    }
}
