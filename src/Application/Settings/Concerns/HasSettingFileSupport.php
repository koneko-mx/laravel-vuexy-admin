<?php

namespace Koneko\VuexyAdmin\Application\Settings\Concerns;

use Illuminate\Http\UploadedFile;

trait HasSettingFileSupport
{
    protected array $file = [
        'mime_type' => null,
        'file_name' => null,
    ];

    public function enableFile(bool $state = true): static
    {
        $this->attributes['is_file'] = $state;
        return $this;
    }

    public function file(string $mime_type, string $file_name): static
    {
        $this->attributes['is_file'] = true;
        $this->file['mime_type'] = $mime_type;
        $this->file['file_name'] = $file_name;

        return $this;
    }

    public function mimeType(string $mime_type): static
    {
        $this->attributes['is_file'] = true;
        $this->file['mime_type'] = $mime_type;

        return $this;
    }

    public function fileName(string $file_name): static
    {
        $this->attributes['is_file'] = true;
        $this->file['file_name'] = $file_name;

        return $this;
    }

    public function handleFileUpload(UploadedFile $file, string $storageDisk = 'public'): static
    {
        $path = $file->store('settings_files', $storageDisk);

        $this->file(
            mime_type: $file->getMimeType() ?? 'application/octet-stream',
            file_name: basename($path)
        );

        return $this;
    }


    // ==================== Validaciones ====================

    protected function validateFile(): void
    {
        if ($this->attributes['is_file']) {
            if (empty($this->file['mime_type']) || empty($this->file['file_name'])) {
                throw new \InvalidArgumentException("Se requiere 'mime_type' y 'file_name' para archivos.");
            }
        }
    }
}
