<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Traits\User;

trait HasProfilePhoto
{
    /**
     * Get the URL for the user's profile photo.
     *
     * @return string
     */
    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo_path) {
            return asset('storage/profile-photos/' . $this->profile_photo_path);
        }

        return $this->defaultProfilePhotoUrl();
    }

    /**
     * Get the default profile photo URL if no profile photo has been uploaded.
     *
     * @return string
     */
    protected function defaultProfilePhotoUrl()
    {
        return route('admin.core.user.avatar.image', ['name' => $this->full_name]);
    }
}
