<?php

namespace StrmPrivacy\Driver\Enums;

use ReflectionClass;

abstract class Enum
{
    public static function all(): array
    {
        $reflectionClass = new ReflectionClass(static::class);

        return $reflectionClass->getConstants();
    }

    public static function isValid(string $value): bool
    {
        return in_array($value, static::all());
    }
}
