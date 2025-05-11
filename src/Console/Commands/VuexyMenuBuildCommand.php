<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Koneko\VuexyAdmin\Models\User;
use Koneko\VuexyAdmin\Application\UX\Menu\VuexyMenuBuilderService;
use Koneko\VuexyAdmin\Application\UX\Menu\VuexyMenuRegistry;
use Symfony\Component\Console\Attribute\AsCommand;

/**
 * Comando Artisan para construir, cachear, analizar y validar el menú Vuexy.
 */
#[AsCommand(name: 'vuexy:menu:build')]
class VuexyMenuBuildCommand extends Command
{
    protected $signature = 'vuexy:menu:build
        {--fresh : Limpia el caché del menú}
        {--json : Mostrar menú como JSON}
        {--dump : Hacer dump de la estructura final}
        {--raw : Mostrar menú crudo desde módulos}
        {--validate : Valida el menú combinado}
        {--user= : ID o email del usuario}
        {--role= : ID o nombre del rol}
        {--visitor : Menú de visitante}
        {--summary : Mostrar resumen de claves}
        {--id-node= : Mostrar nodo por auto_id}';

    protected $description = 'Construye, cachea, analiza y valida el menú principal de Vuexy';

    public function handle(): int
    {
        $fresh     = $this->option('fresh');
        $showJson  = $this->option('json');
        $showDump  = $this->option('dump');
        $showRaw   = $this->option('raw');
        $validate  = $this->option('validate');
        $summary   = $this->option('summary');
        $userInput = $this->option('user');
        $roleInput = $this->option('role');
        $visitor   = $this->option('visitor');
        $targetId  = $this->option('id-node');

        $user = null;

        if (is_string($targetId) && is_numeric($targetId)) {
            $targetId = (int) $targetId;
        }

        if ($visitor) {
            $this->info('Menú para visitante:');
            $menu = app(VuexyMenuBuilderService::class)->getForUser(null);
            $this->renderMenu($menu, $showJson, $showDump, $summary, $targetId);
            return self::SUCCESS;
        }

        if ($userInput) {
            $user = $this->resolveUser($userInput);
            if (!$user) {
                $this->error("Usuario no encontrado: $userInput");
                return self::FAILURE;
            }
        }

        if ($roleInput) {
            $user = $this->simulateUserWithRole($roleInput);
            if (!$user) {
                $this->error("Rol no encontrado: $roleInput");
                return self::FAILURE;
            }
        }

        if ($fresh) {
            $user
                ? VuexyMenuBuilderService::clearCacheForUser($user)
                : VuexyMenuBuilderService::clearAllCache();
            $this->info('Caché de menú limpiado.');
        }

        if ($showRaw || $validate) {
            $raw = (new VuexyMenuRegistry())->getMerged();

            if ($validate) {
                $this->info("Validando estructura de menú...");
                $this->validateMenu($raw);
                return self::SUCCESS;
            }

            $this->line("Menú crudo desde módulos:");
            $summary ? $this->summarizeMenu($raw) : $this->dumpOrJson($raw, $showJson, $showDump);
            return self::SUCCESS;
        }

        if (!$user) {
            $user = Auth::user();
            if (!$user && !$fresh) {
                $this->error('No hay sesión activa. Usa --user o --role.');
                return self::FAILURE;
            }
        }

        $menu = app(VuexyMenuBuilderService::class)->getForUser($user);
        $this->renderMenu($menu, $showJson, $showDump, $summary, $targetId);
        return self::SUCCESS;
    }

    protected function validateMenu(array $menu, string $prefix = ''): void
    {
        foreach ($menu as $key => $item) {
            if (!isset($item['_meta'])) {
                $this->error("[!] El item '$prefix$key' no contiene '_meta'");
                continue;
            }

            $required = ['icon', 'description'];
            foreach ($required as $field) {
                if (!isset($item['_meta'][$field])) {
                    $this->warn("[!] '$prefix$key' está incompleto: falta '$field'");
                }
            }

            if (isset($item['route']) && !\Illuminate\Support\Facades\Route::has($item['route'])) {
                $this->warn("[!] Ruta inexistente en '$prefix$key': {$item['route']}");
            }

            if (isset($item['submenu'])) {
                $this->validateMenu($item['submenu'], $prefix . "$key/");
            }
        }
    }

    protected function renderMenu(array $menu, bool $json, bool $dump, bool $summary, ?int $id = null): void
    {
        if ($id !== null) {
            $target = $this->findById($menu, $id);
            if (!$target) {
                $this->error("Nodo ID #$id no encontrado");
                return;
            }
            $this->line("Nodo con ID #$id:");
            $this->dumpOrJson($target, $json, $dump);
            return;
        }

        $summary
            ? $this->summarizeMenu($menu)
            : $this->dumpOrJson($menu, $json, $dump);
    }

    protected function dumpOrJson(array $menu, bool $asJson, bool $asDump): void
    {
        if ($asJson) {
            $this->line(json_encode($menu, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } elseif ($asDump) {
            dump($menu);
        } else {
            $this->warn('Agrega --json, --dump o --summary para mostrar el menú.');
        }
    }

    protected function summarizeMenu(array $menu, string $prefix = ''): void
    {
        foreach ($menu as $key => $item) {
            $id = $item['_meta']['auto_id'] ?? '---';
            $this->line("[#" . str_pad((string) $id, 3, ' ', STR_PAD_LEFT) . "] $prefix$key");
            if (isset($item['submenu'])) {
                $this->summarizeMenu($item['submenu'], $prefix . '  └ ');
            }
        }
    }

    protected function findById(array $menu, int $id): ?array
    {
        foreach ($menu as $item) {
            if (($item['_meta']['auto_id'] ?? null) === $id) {
                return $item;
            }
            if (isset($item['submenu'])) {
                $found = $this->findById($item['submenu'], $id);
                if ($found) return $found;
            }
        }
        return null;
    }

    protected function resolveUser(int|string $user): ?User
    {
        return is_numeric($user)
            ? User::find((int) $user)
            : User::where('email', (string) $user)->first();
    }

    protected function simulateUserWithRole(string $roleName): ?User
    {
        $role = \Spatie\Permission\Models\Role::where('name', $roleName)->first();
        if (!$role) return null;

        $user = new User();
        $user->id = 0;
        $user->email = 'simulado@vuexy.test';
        $user->name = '[Simulado: ' . $roleName . ']';
        $user->setRelation('roles', collect([$role]));
        $user->syncRoles($role);
        return $user;
    }
}