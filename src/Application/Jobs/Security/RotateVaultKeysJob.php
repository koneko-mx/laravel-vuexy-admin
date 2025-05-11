<?php

namespace Koneko\VuexyAdmin\Application\Jobs\Security;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};
use Koneko\VuexyAdmin\Application\Security\VaultKeyService;
use Koneko\VuexyAdmin\Models\VaultKey;
use Illuminate\Support\Facades\Log;

class RotateVaultKeysJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected ?int $rotationThresholdDays;

    public function __construct(?int $rotationThresholdDays = null)
    {
        $this->rotationThresholdDays = $rotationThresholdDays;
    }

    public function handle(): void
    {
        $query = VaultKey::query()->where('is_active', true);

        if ($this->rotationThresholdDays) {
            $query->where('rotated_at', '<=', now()->subDays($this->rotationThresholdDays));
        }

        $keysToRotate = $query->get();

        if ($keysToRotate->isEmpty()) {
            Log::info('🔄 No hay claves que requieran rotación en este ciclo.');
            return;
        }

        $service = app(VaultKeyService::class);

        foreach ($keysToRotate as $key) {
            try {
                $service->rotateKey($key->alias);
                Log::info("✅ Clave '{$key->alias}' rotada correctamente.");

            } catch (\Throwable $e) {
                Log::error("❌ Error al rotar la clave '{$key->alias}': " . $e->getMessage());
            }
        }
    }
}
