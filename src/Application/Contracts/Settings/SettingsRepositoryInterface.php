<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Contracts\Settings;

use Koneko\VuexyAdmin\Models\Setting;

/**
 * Contrato para los servicios de gestión de Settings modulares.
 * Proporciona una API fluida para acceder y modificar configuraciones de manera modular.
 */
interface SettingsRepositoryInterface
{
    public function set(string $key, mixed $value): ?Setting;

    public function get(string $key, mixed $default = null): mixed;

    public function delete(string $key): bool;

    public function exists(string $key): bool;


    public function markAsSystem(bool $state = true): static;
    public function markAsEncrypted(bool $state = true): static;
    public function markAsSensitive(bool $state = true): static;
    public function markAsEditable(bool $state = true): static;
    public function markAsActive(bool $state = true): static;


    public function withoutUsageTracking(): static;
}
