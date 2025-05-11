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
    public function get(string $key, ...$args): mixed;

    public function set(string $key, mixed $value, ?int $userId = null, ...$args): ?Setting;

    public function delete(string $key, ?int $userId = null): bool;

    public function exists(string $key): bool;

    public function getGroup(string $group, ?int $userId = null): array;

    public function getComponent(string $component, ?int $userId = null): array;

    public function deleteGroup(string $group, ?int $userId = null): int;

    public function deleteComponent(string $component, ?int $userId = null): int;

    public function listGroups(): array;

    public function listComponents(): array;

    public function currentNamespace(): string;

    public function currentGroup(): string;

    public function setContext(string $component, string $group): static;

    public function setComponent(string $component): static;

    public function setGroup(string $group): static;
}
