<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Services;

use Illuminate\Support\{Arr,Str};
use Illuminate\Support\Facades\{Http,File};
use Koneko\VuexyAdmin\Application\Enums\ModuleSourceType;
use Koneko\VuexyAdmin\Models\ModulePackage;
use Koneko\VuexyAdmin\Support\Parsers\ComposerJsonParser;
use Symfony\Component\Process\Process;

class ModulePackageAnalyzerService
{
    /**
     * Analiza un paquete basado en su URL de Packagist o Github (o ZIP opcional).
     */
    public function analyzeFromUrl(string $url): array
    {
        if (Str::contains($url, 'packagist.org/packages/')) {
            return $this->analyzePackagist($url);
        }

        if (Str::contains($url, 'github.com/')) {
            return $this->analyzeGithubRepo($url);
        }

        // En el futuro podríamos agregar ZIP local o descarga
        throw new \InvalidArgumentException('Tipo de URL no soportado: ' . $url);
    }

    /**
     * Analiza un paquete de Packagist.
     */
    protected function analyzePackagist(string $url): array
    {
        $parts = explode('/packages/', $url);

        if (count($parts) !== 2) {
            throw new \InvalidArgumentException('URL de Packagist inválida: ' . $url);
        }

        $packageName = $parts[1];

        $apiUrl = "https://repo.packagist.org/p2/{$packageName}.json";

        $response = Http::timeout(10)->get($apiUrl);

        if (!$response->successful()) {
            throw new \RuntimeException('Error al conectar con Packagist para ' . $packageName);
        }

        $packageData = $response->json()['packages'][$packageName][0] ?? [];

        return $this->normalizeComposerData($packageData, $url);
    }

    /**
     * Analiza un repositorio Github (composer.json en raíz).
     */
    protected function analyzeGithubRepo(string $url): array
    {
        $composerUrl = $url;

        if (!Str::endsWith($url, 'composer.json')) {
            $composerUrl = rtrim($url, '/') . '/raw/main/composer.json';
        }

        $response = Http::timeout(10)->get($composerUrl);

        if (!$response->successful()) {
            throw new \RuntimeException('No se pudo recuperar el composer.json de GitHub: ' . $composerUrl);
        }

        $packageData = $response->json();

        return $this->normalizeComposerData($packageData, $url);
    }

    /**
     * Analiza un repositorio Git (privado o público) y extrae metadatos del composer.json.
     */
    public function analyzeFromGit(string $repoUrl): ?array
    {
        $tempDir = storage_path('app/tmp/module-analyzer/' . Str::random(16));
        File::ensureDirectoryExists($tempDir);

        try {
            $this->cloneRepository($repoUrl, $tempDir);

            $composerPath = $tempDir . '/composer.json';
            if (!File::exists($composerPath)) {
                throw new \RuntimeException("composer.json no encontrado en el repositorio");
            }

            $json = json_decode(File::get($composerPath), true);
            if (!is_array($json)) {
                throw new \RuntimeException("composer.json inválido");
            }

            return ComposerJsonParser::buildModuleMetadata($json, $repoUrl);

        } finally {
            File::deleteDirectory($tempDir);
        }
    }

    /**
     * Clona un repositorio Git (SSH o HTTPS).
     */
    protected function cloneRepository(string $url, string $destination): void
    {
        $process = new Process(['git', 'clone', '--depth=1', $url, $destination]);
        $process->setTimeout(30);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException("Error al clonar el repositorio: " . $process->getErrorOutput());
        }
    }

    /**
     * Normaliza los datos obtenidos desde Packagist o Github.
     */
    protected function normalizeComposerData(array $composer, string $sourceUrl): array
    {
        $author = Arr::first($composer['authors'] ?? []) ?? [];

        return [
            'name'          => $composer['name'] ?? 'unknown/package',
            'display_name'  => $composer['name'] ?? 'Unknown Package',
            'description'   => $composer['description'] ?? '',
            'keywords'      => $composer['keywords'] ?? [],
            'author_name'   => $author['name'] ?? null,
            'author_email'  => $author['email'] ?? null,
            'source_url'    => $sourceUrl,
            'composer_url'  => $sourceUrl,
            'cover_image'   => null,
            'readme_path'   => null,
            'source_type'   => ModuleSourceType::Official, // por ahora
            'zip_available' => false,
            'composer'      => $composer,
            'repository_type' => 'public',
            'active' => true,
        ];
    }

    /**
     * Guarda o actualiza el paquete en la base de datos.
     */
    public function saveOrUpdatePackage(array $data): ModulePackage
    {
        return ModulePackage::updateOrCreate(
            ['name' => $data['name']],
            $data
        );
    }
}
