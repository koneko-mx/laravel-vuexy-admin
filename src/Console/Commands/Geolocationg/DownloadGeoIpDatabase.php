<?php

namespace Koneko\VuexyAdmin\Console\Commands\Geolocationg;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class DownloadGeoIpDatabase extends Command
{
    protected $signature = 'geoip:download
                            {--license= : Tu licencia de MaxMind (opcional si ya está en .env)}
                            {--path= : Ruta personalizada de descarga (por defecto: public/vendor/geoip)}';

    protected $description = 'Descarga la base de datos GeoLite2-City.mmdb desde MaxMind.';

    public function handle(): void
    {
        $licenseKey = $this->option('license') ?? env('MAXMIND_LICENSE_KEY');
        $downloadPath = base_path($this->option('path') ?? 'public/vendor/geoip');
        $fileName = 'GeoLite2-City.mmdb';

        if (!$licenseKey) {
            $this->error('⚠️ No se proporcionó ninguna licencia de MaxMind. Usa --license= o configura MAXMIND_LICENSE_KEY en .env');
            return;
        }

        $url = "https://download.maxmind.com/app/geoip_download?edition_id=GeoLite2-City&license_key={$licenseKey}&suffix=tar.gz";

        $this->info("⏬ Descargando GeoLite2-City.mmdb desde MaxMind...");
        $tmpPath = storage_path('app/geoip.tar.gz');

        try {
            $response = Http::withOptions(['sink' => $tmpPath])->get($url);

            if (! $response->ok()) {
                $this->error('❌ Falló la descarga. Verifica tu licencia o conexión.');
                return;
            }

            $this->info("✅ Descarga completada. Extrayendo archivo...");

            $tar = new \PharData($tmpPath);
            $tar->decompress(); // crea .tar
            $untar = str_replace('.gz', '', $tmpPath);
            $archive = new \PharData($untar);
            $archive->extractTo(storage_path('app/geoip_extracted'), null, true);

            $files = File::allFiles(storage_path('app/geoip_extracted'));
            $mmdb = collect($files)->first(fn($f) => str_ends_with($f->getFilename(), '.mmdb'));

            if (! $mmdb) {
                $this->error('⚠️ No se encontró el archivo .mmdb en el paquete descargado.');
                return;
            }

            File::ensureDirectoryExists($downloadPath);
            File::copy($mmdb->getRealPath(), "{$downloadPath}/{$fileName}");

            $this->info("🎉 Base de datos copiada a {$downloadPath}/{$fileName}");

        } catch (\Throwable $e) {
            $this->error("🚨 Error inesperado: {$e->getMessage()}");
        }
    }
}
