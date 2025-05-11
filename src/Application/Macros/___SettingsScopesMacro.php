<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Macros;

use Illuminate\Support\Facades\App;

App::macro('settings', function () {
    $settingsService = app(\Koneko\VuexyAdmin\Application\System\SettingsService::class);

    return new class($settingsService) {
        public function __construct(private $settingsService) {}

        public function get(string $key, ...$args): mixed
        {
            return $this->settingsService->get($key, ...$args);
        }

        public function set(string $key, mixed $value, ...$args): mixed
        {
            return $this->settingsService->set($key, $value, ...$args);
        }

        public function admin(): object
        {
            return new class($this->settingsService) {
                public function __construct(private $settingsService) {}

                public function get(string $key, ...$args): mixed
                {
                    return $this->settingsService->get('koneko.admin.' . $key, ...$args);
                }

                public function set(string $key, mixed $value, ...$args): mixed
                {
                    return $this->settingsService->set('koneko.admin.' . $key, $value, ...$args);
                }
            };
        }
    };
});
