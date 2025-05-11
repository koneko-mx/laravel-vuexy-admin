<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Parsers;

use Illuminate\Support\Str;

class ComposerJsonParser
{
    public static function buildModuleMetadata(array $composer, ?string $sourceUrl = null): array
    {
        $nameParts = explode('/', $composer['name'] ?? 'unknown/module');

        return [
            'name'         => $composer['name'] ?? 'unknown/module',
            'slug'         => $nameParts[1] ?? 'module',
            'display_name' => Str::headline($nameParts[1] ?? 'module'),
            'description'  => $composer['description'] ?? null,
            'keywords'     => $composer['keywords'] ?? [],
            'author_name'  => data_get($composer, 'authors.0.name'),
            'author_email' => data_get($composer, 'authors.0.email'),
            'support'      => $composer['support'] ?? [],
            'license'      => $composer['license'] ?? null,
            'minimum_stability' => $composer['minimum-stability'] ?? null,
            'source_url'   => $sourceUrl,
            'composer'     => $composer,
            'build_version'=> now()->format('YmdHis'),
            'repository_type' => 'public',
        ];
    }
}
