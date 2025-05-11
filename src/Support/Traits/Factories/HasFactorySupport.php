<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Traits\Factories;

use Illuminate\Support\Str;

trait HasFactorySupport
{
    protected int $defaultMaybe = 30;

    protected function maybeDefault(mixed $value): mixed
    {
        return $this->maybe($this->defaultMaybe, $value);
    }

    protected function maybe(int $percentage, mixed $value): mixed
    {
        return rand(1, 100) <= $percentage
            ? (is_callable($value) ? $value() : $value)
            : null;
    }

    protected function randomFrom(array $options, mixed $fallback = null): mixed
    {
        return !empty($options) ? $this->faker->randomElement($options) : $fallback;
    }

    protected function generateNiceEmail(): string
    {
        $first  = Str::slug($this->faker->firstName);
        $last   = Str::slug($this->faker->lastName);
        $domain = 'clientes.test';
        $hash   = substr(md5(uniqid($first . $last, true)), 0, 6);

        return "{$first}.{$last}.{$hash}@{$domain}";
    }

    protected function fakeDateRange(string $start = '-1 year', string $end = 'now'): array
    {
        $start = $this->faker->dateTimeBetween($start, $end);
        $end   = (clone $start)->modify('+' . rand(30, 360) . ' days');

        return [$start, $end];
    }

    protected function fakeEnumValue(string $enumClass): string
    {
        return $this->enumRandomCase($enumClass)->value;
    }

    public function enumRandomCase(string $enumClass, int $count = 1): mixed
    {
        return method_exists($enumClass, 'random')
            ? $enumClass::random($count)
            : throw new \InvalidArgumentException("Enum {$enumClass} debe implementar ::random()");
    }

    public function debugFake(int $count = 10): void
    {
        for ($i = 0; $i < $count; $i++) {
            dump($this->definition());
        }

        exit("🛑 Debug finalizado.\n");
    }
}

