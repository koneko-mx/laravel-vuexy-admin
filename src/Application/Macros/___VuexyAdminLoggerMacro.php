<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Macros;

use Illuminate\Support\Facades\Log;

Log::macro('vuexyAdminLogger', function () {
    return new class {
        public function info(string $message, array $context = []) {
            return Log::system()->info('vuexy-admin', $message, $context);
        }

        public function warning(string $message, array $context = []) {
            return Log::system()->warning('vuexy-admin', $message, $context);
        }

        public function error(string $message, array $context = []) {
            return Log::system()->error('vuexy-admin', $message, $context);
        }

        public function debug(string $message, array $context = []) {
            return Log::system()->debug('vuexy-admin', $message, $context);
        }
    };
});
