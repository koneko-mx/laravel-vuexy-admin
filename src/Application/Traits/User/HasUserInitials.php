<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Traits\User;

use Koneko\VuexyAdmin\Models\User;

trait HasUserInitials
{
    /**
     * Get the initials of the user's full name.
     *
     * @return string
     */
    public function getInitialsAttribute()
    {
        return self::getInitials(trim($this->name . ' ' . $this->last_name));
    }

    /**
     * Calcula las iniciales a partir del nombre.
     *
     * @param string $name Nombre completo.
     *
     * @return string Iniciales en mayúsculas.
     */
    public static function getInitials($name)
    {
        if (empty($name)) {
            return 'NA';
        }

        $initials = implode('', array_map(function ($word) {
            return mb_substr($word, 0, 1);
        }, explode(' ', $name)));

        return strtoupper(substr($initials, 0, User::$initialMaxLength?? 3));
    }
}
