<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\UI\Avatar;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageInterface;
use Symfony\Component\Mime\MimeTypes;

class AvatarImageService
{
    protected string $avatarDisk = 'public';
    protected string $profilePhotoDir = 'profile-photos';
    protected int $avatarWidth = 512;
    protected int $avatarHeight = 512;
    protected string $defaultFitMethod = 'cover'; // cover, contain, fill, fit-width, fit-height

    public function __construct()
    {
        $this->configureFromSettings();
    }

    protected function configureFromSettings(): void
    {
        $config = config_m('core')->get('ui.avatar.image', []);

        $this->avatarDisk       = $config['disk'] ?? $this->avatarDisk;
        $this->profilePhotoDir  = $config['directory'] ?? $this->profilePhotoDir;
        $this->avatarWidth      = (int) $config['width'] ?? $this->avatarWidth;
        $this->avatarHeight     = (int) $config['height'] ?? $this->avatarHeight;
        $this->defaultFitMethod = $config['fit_method'] ?? $this->defaultFitMethod;
    }

    public function updateProfilePhoto(Model $user, string|UploadedFile $imageSource): string
    {
        $file = $this->ensureUploadedFile($imageSource);
        $this->validateImageFile($file);

        $avatarName = $this->generateAvatarFilename();
        $image = $this->processImage($file);

        $this->saveAvatar($image, $avatarName);
        $this->updateUserProfilePhoto($user, $avatarName);

        return $avatarName;
    }

    protected function ensureUploadedFile(string|UploadedFile $imageSource): UploadedFile
    {
        if ($imageSource instanceof UploadedFile) {
            return $imageSource;
        }

        if (!file_exists($imageSource)) {
            throw new \InvalidArgumentException('El archivo no existe en la ruta especificada.');
        }

        $mime = (new MimeTypes())->guessMimeType($imageSource);
        return new UploadedFile(
            $imageSource,
            basename($imageSource),
            $mime,
            null,
            true
        );
    }

    protected function validateImageFile(UploadedFile $file): void
    {
        $validExtensions = ['jpg', 'jpeg', 'png', 'webp', 'bmp', 'svg', 'gif', 'tiff', 'tif'];
        $extension = strtolower($file->getClientOriginalExtension());

        if (!in_array($extension, $validExtensions)) {
            throw new \InvalidArgumentException(
                'Formato de archivo no válido. Formatos aceptados: ' . implode(', ', $validExtensions)
            );
        }
    }

    protected function generateAvatarFilename(): string
    {
        return uniqid('avatar_') . '.png';
    }

    protected function processImage(UploadedFile $file): ImageInterface
    {
        $manager = new ImageManager(config('image.driver', 'gd'));
        $image = $manager->read($file->getRealPath());

        return match($this->defaultFitMethod) {
            'cover' => $this->processCover($image),
            'contain' => $this->processContain($image),
            'fill' => $this->processFill($image),
            'fit-width' => $this->processFitWidth($image),
            'fit-height' => $this->processFitHeight($image),
            default => $this->processCover($image),
        };
    }

    protected function processCover(ImageInterface $image): ImageInterface
    {
        return $image->cover($this->avatarWidth, $this->avatarHeight);
    }

    protected function processContain(ImageInterface $image): ImageInterface
    {
        return $image->contain($this->avatarWidth, $this->avatarHeight);
    }

    protected function processFill(ImageInterface $image): ImageInterface
    {
        $canvas = (new ImageManager(config('image.driver', 'gd')))
            ->create($this->avatarWidth, $this->avatarHeight, 'rgba(0, 0, 0, 0)');

        $image->scale($this->avatarWidth, $this->avatarHeight);
        $canvas->place($image, 'center');

        return $canvas;
    }

    protected function processFitWidth(ImageInterface $image): ImageInterface
    {
        return $image->scale(width: $this->avatarWidth);
    }

    protected function processFitHeight(ImageInterface $image): ImageInterface
    {
        return $image->scale(height: $this->avatarHeight);
    }

    protected function saveAvatar(ImageInterface $image, string $filename): void
    {
        $this->ensureProfilePhotoDirectoryExists();

        Storage::disk($this->avatarDisk)->put(
            $this->profilePhotoDir . '/' . $filename,
            $image->toPng()
        );
    }

    protected function ensureProfilePhotoDirectoryExists(): void
    {
        if (!Storage::disk($this->avatarDisk)->exists($this->profilePhotoDir)) {
            Storage::disk($this->avatarDisk)->makeDirectory($this->profilePhotoDir);
        }
    }

    protected function updateUserProfilePhoto(Model $user, string $filename): void
    {
        $this->deleteProfilePhoto($user);

        DB::table('users')
            ->where('email', $user->email)
            ->update(['profile_photo_path' => $filename]);
    }

    public function deleteProfilePhoto(Model $user): void
    {
        if (!empty($user->profile_photo_path)) {
            Storage::disk($this->avatarDisk)->delete($user->profile_photo_path);
            $user->forceFill(['profile_photo_path' => null])->save();
        }
    }

    public function clearAllProfilePhotos(): void
    {
        Storage::disk($this->avatarDisk)->deleteDirectory($this->profilePhotoDir);
        $this->ensureProfilePhotoDirectoryExists();
    }
}
