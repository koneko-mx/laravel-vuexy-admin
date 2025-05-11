<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Traits\Factories;

use Spatie\Permission\Models\Role;

trait HasUserFactoryRoleExtension
{
    public function withRoles(int $min = 1, int $max = 2): self
    {
        return $this->afterCreating(function ($user) use ($min, $max) {
            $roles = Role::inRandomOrder()
                ->take(rand($min, $max))
                ->pluck('name');

            if ($roles->isNotEmpty()) {
                $user->assignRole($roles);
            }
        });
    }
}
