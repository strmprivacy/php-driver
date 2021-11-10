<?php

namespace StrmPrivacy\Driver\Serializers;

use InvalidArgumentException;
use StrmPrivacy\Driver\Contracts\Serializer;
use StrmPrivacy\Driver\Enums\SerializationType;

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
