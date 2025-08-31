<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Media\Image;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Intervention\Image\ImageManager;

/**
 * ImageCore: base minimalista, sin rutas fijas ni settings.
 * - Driver preferido imagick (fallback gd)
 * - Disco configurable y conmutables via withDisk()
 * - PathResolver externo para carpetas por contexto (module/scope/owner)
 */
class ImageCore
{
    protected string $disk;
    protected ImageManager $im;
    protected PathResolver $paths;
    protected array $context = []; // module/scope/owner...

    public function __construct(?string $disk = null, ?string $driver = null, ?PathResolver $paths = null)
    {
        $this->disk  = $disk   ?? config('koneko.media.default_disk', 'public');
        $driver      = $driver ?? config('image.driver', 'gd');
        $this->im    = new ImageManager($driver);
        $this->paths = $paths  ?? new PathResolver(); // usa config por defecto
    }

    public function withDisk(string $disk): self
    {
        $clone = clone $this;
        $clone->disk = $disk;
        return $clone;
    }

    /** Guarda contexto (e.g. ['module'=>'website','scope'=>'site','owner'=>123]) */
    public function forContext(array $ctx): self
    {
        $clone = clone $this;
        $clone->context = $ctx;
        return $clone;
    }

    // ---------- utils ----------
    protected function ensureDir(string $dir): void
    {
        Storage::disk($this->disk)->makeDirectory($dir);
    }

    protected function toPublicUrl(string $relative): string
    {
        return asset('storage/' . ltrim($relative, '/'));
    }

    protected function uniq(string $prefix, string $ext): string
    {
        return \uniqid($prefix . '_', true) . '.' . ltrim($ext, '.');
    }

    protected function deleteIfExists(array $paths): void
    {
        foreach ($paths as $p) {
            if ($p && !str_starts_with($p, 'data:') && Storage::disk($this->disk)->exists($p)) {
                Storage::disk($this->disk)->delete($p);
            }
        }
    }

    protected function dimsFromAreaAspect(int $area, float $aspect): array
    {
        $aspect = max(0.05, $aspect);
        $w = (int) round(\sqrt($area * $aspect));
        $h = max(1, (int) round($w / $aspect));
        return [$w, $h];
    }

    // ---------- operaciones de alto nivel ----------

    /** Favicons (cover WxH). $sizes = ['16x16'=>[16,16], ...] */
    public function makeFavicons(UploadedFile $file, array $sizes, ?string $dirKey = 'favicon', string $prefix = 'favicon'): array
    {
        $dir = $this->paths->resolve($dirKey, $this->context); // p.ej "favicon/website/site/123"
        $this->ensureDir($dir);

        $src = $this->im->read($file->getRealPath());
        $out = [];

        foreach ($sizes as $label => [$w,$h]) {
            $img = clone $src;
            $img = $img->cover($w, $h);
            $name = $this->uniq($prefix.'_'.$label, 'png');
            $path = "$dir/$name";
            Storage::disk($this->disk)->put($path, $img->toPng(indexed: true));
            $out[$label] = $path;
        }

        $file->delete();
        return $out;
    }

    /**
     * Share (OG/Twitter) por área y aspecto, con fit.
     * $opts: area(756000), aspect(1.91), fit(cover|contain|stretch), bg(#fff), format(auto|jpg|webp|png), quality(80), dirKey('share'), prefix('og')
     */
    public function makeShare(UploadedFile $file, array $opts = []): string
    {
        $opts = array_merge([
            'area'    => (int) config('koneko.media.share.default_area', 756000),
            'aspect'  => (float) config('koneko.media.share.default_aspect', 1.91),
            'fit'     => (string) config('koneko.media.share.default_fit', 'cover'), // cover|keep|contain|stretch
            'bg'      => (string) config('koneko.media.share.default_bg', '#ffffff'),
            'format'  => (string) config('koneko.media.share.default_format', 'auto'),
            'quality' => (int)    config('koneko.media.share.default_quality', 80),
            'dirKey'  => 'share',
            'prefix'  => 'og',
        ], $opts);

        $dir = $this->paths->resolve($opts['dirKey'], $this->context);
        $this->ensureDir($dir);

        $src = $this->im->read($file->getRealPath());

        // dims objetivo para cover/contain
        [$tw,$th] = $this->dimsFromAreaAspect((int)$opts['area'], (float)$opts['aspect']);

        if ($opts['fit'] === 'cover') {
            $dst = (clone $src)->cover($tw, $th);
        }
        elseif ($opts['fit'] === 'keep') {
            // No recorte, sin distorsión. Usamos el área + aspect para calcular destino
            $dst = (clone $src);
            if ($opts['upscale']) {
                $dst = $dst->resize($tw, $th, function ($c) {
                    $c->aspectRatio();        // permite upscale
                });
            } else {
                $dst = $dst->scaleDown(width: $tw, height: $th); // nunca escala arriba
            }
        }
        elseif ($opts['fit'] === 'contain') {
            // (opcional) el modo original con relleno si aún lo usas en otros lados
            $dst = $this->im->create($tw, $th)->fill($opts['bg']);
            $tmp = (clone $src);
            $tmp->scaleDown(width: $tw, height: $th);
            $x = (int)(($tw - $tmp->width()) / 2);
            $y = (int)(($th - $tmp->height()) / 2);
            $dst->place($tmp, 'top-left', $x, $y);
        }
        else {
            // fallback sensato
            $dst = (clone $src)->cover($tw, $th);
        }


        // Formato de salida
        $fmt = $opts['format'];
        if ($fmt === 'auto') {
            $fmt = $this->inferAutoFormat($file); // jpg|png|webp según fuente
        }

        $name = $this->uniq($opts['prefix'], $fmt);
        $path = "$dir/$name";

        if (in_array($fmt, ['jpg', 'jpeg'], true)) {
            Storage::disk($this->disk)->put($path, $dst->toJpg((int) $opts['quality']));
        } elseif ($fmt === 'webp') {
            Storage::disk($this->disk)->put($path, $dst->toWebp((int) $opts['quality']));
        } else {
            Storage::disk($this->disk)->put($path, $dst->toPng(indexed: true));
        }

        $file->delete();
        return $path;
    }

    /** Mantiene el formato de origen cuando se usa format=auto */
    private function inferAutoFormat(UploadedFile $file): string
    {
        $ext = strtolower($file->getClientOriginalExtension() ?: pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION));
        return match ($ext) {
            'png'  => 'png',
            'webp' => 'webp',
            default => 'jpg',
        };
    }


    /** Logos por área, manteniendo aspecto (sin recorte) */
    public function makeLogoByArea(UploadedFile $file, int $area, ?string $dirKey = 'logo', string $suffix = 'large'): string
    {
        $dir = $this->paths->resolve($dirKey, $this->context);
        $this->ensureDir($dir);

        $src = $this->im->read($file->getRealPath());
        $ratio = $src->width() / max(1, $src->height());
        [$w,$h] = $this->dimsFromAreaAspect($area, $ratio);

        $dst = (clone $src)->resize($w, $h, function ($c) { $c->aspectRatio(); $c->upsize(); });
        $name = $this->uniq('logo_'.$suffix, 'png');
        $path = "$dir/$name";

        Storage::disk($this->disk)->put($path, $dst->toPng(indexed: true));
        $file->delete();

        return $path;
    }
}
