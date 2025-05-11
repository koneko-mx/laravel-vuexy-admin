<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Enums\ExternalApi;

enum ApiProvider: string
{
    case Google     = 'google';
    case Banxico    = 'banxico';
    case SAT        = 'sat';
    case Custom     = 'custom';
    case Esys       = 'esys';
    case Facebook   = 'facebook';
    case Twitter    = 'twitter';
    case TawkTo     = 'tawk_to';

    public function label(): string
    {
        return match ($this) {
            self::Google  => 'Google',
            self::Banxico => 'Banxico',
            self::SAT     => 'SAT (México)',
            self::Custom  => 'Personalizado',
            self::Esys    => 'ESYS',
            self::Facebook => 'Facebook',
            self::Twitter => 'Twitter',
            self::TawkTo  => 'Tawk.to',
        };
    }
}