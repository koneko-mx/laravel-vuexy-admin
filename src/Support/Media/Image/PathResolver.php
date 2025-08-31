<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Media\Image;

/**
 * Resuelve rutas con plantillas de config.
 * Ej: 'share' => 'media/{module}/{scope}/{owner}/share'
 */
class PathResolver
{
    public function resolve(string $key, array $ctx = []): string
    {
        $tpl = (string) config("koneko_media.paths.$key", "media/$key");
        $rep = [
            '{module}' => $ctx['module'] ?? 'core',
            '{scope}'  => $ctx['scope']  ?? 'global',
            '{owner}'  => (string) ($ctx['owner'] ?? 'common'),
        ];
        $out = strtr($tpl, $rep);
        return trim($out, '/');
    }
}
