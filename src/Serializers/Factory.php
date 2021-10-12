<?php

namespace Streammachine\Driver\Serializers;

use InvalidArgumentException;
use Streammachine\Driver\Contracts\Serializer;
use Streammachine\Driver\Enums\SerializationType;

class Factory
{
    protected const TYPES = [
        SerializationType::JSON => JsonSerializer::class,
        SerializationType::AVRO_BINARY => AvroBinarySerializer::class,
    ];

    public static function create(string $type): Serializer
    {
        if (!isset(self::TYPES[$type])) {
            throw new InvalidArgumentException('Invalid serializationType: ' . $type);
        }

        $class = self::TYPES[$type];

        return new $class();
    }
}
