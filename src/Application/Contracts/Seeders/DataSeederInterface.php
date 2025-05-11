<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Contracts\Seeders;

interface DataSeederInterface
{
    public function run(array $options = []): void;

    public function truncate(): void;

    public function countSeededRecords(): int;
}