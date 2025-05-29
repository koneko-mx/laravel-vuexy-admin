<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Contracts\Files;

interface ParsableFileInterface
{
    /**
     * Parsea el archivo y devuelve un array de registros
     *
     * @throws \RuntimeException Cuando el archivo no puede ser procesado
     */
    public function parse(string $path): array;

    /**
     * Valida la estructura básica del archivo
     */
    public function validate(string $path): bool;
}
