<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Traits\Flags\Factories;

trait HasUserFactoryFlagsExtension
{
    public function withFlags(int $min = 1, int $max = 7): self
    {
        return $this->afterCreating(function ($user) use ($min, $max) {
            $bits = collect(range(0, 7))
                ->shuffle()
                ->take(rand($min, $max))
                ->map(fn ($bit) => 2 ** $bit);

            $user->flags = $bits->sum();
            $user->save();
        });
    }
}
