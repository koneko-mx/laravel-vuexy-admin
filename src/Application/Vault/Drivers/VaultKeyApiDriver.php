<?php

namespace Koneko\VuexyAdmin\Application\Vault\Drivers;

use Illuminate\Support\Facades\Http;
use Koneko\VuexyAdmin\Application\CoreModule;

class VaultKeyApiDriver
{
    protected string $baseUrl;
    protected string $token;
    protected int $timeout;

    public function __construct()
    {
        $config = config_m()->get('security.key_vault.drivers.koneko_api', []);

        $this->baseUrl = rtrim($config['base_url'] ?? '', '/');
        $this->token   = $config['api_token'] ?? '';
        $this->timeout = $config['timeout'] ?? 3;
    }

    public function get(string $project, string $namespace, string $alias): ?string
    {
        $url = "{$this->baseUrl}/{$project}/{$namespace}/{$alias}";

        $response = Http::withToken($this->token)
            ->timeout($this->timeout)
            ->get($url);

        if (!$response->ok()) {
            report("Vault API error: {$response->status()} - {$response->body()}");
            return null;
        }

        $data = $response->json();
        return $data['key_material'] ?? null;
    }
}
