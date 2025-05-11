<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Enums\ExternalApi;

enum ApiAuthType: string
{
    case ApiKey  = 'api_key';
    case OAuth2  = 'oauth2';
    case JWT     = 'jwt';
    case None    = 'none';
    case BearerToken = 'bearer_token';

    public function label(): string
    {
        return match ($this) {
            self::ApiKey => 'API Key',
            self::OAuth2 => 'OAuth 2.0',
            self::JWT    => 'JWT Token',
            self::None   => 'Sin autenticación',
            self::BearerToken => 'Bearer Token',
        };
    }
}
