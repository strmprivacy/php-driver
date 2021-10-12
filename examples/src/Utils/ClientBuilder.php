<?php

namespace Examples\Utils;

use InvalidArgumentException;
use Streammachine\Driver\Client;

class ClientBuilder
{
    protected const DEFAULT_CLASS = Client::class;

    public static function build(array $args, string $subClass = null): Client
    {
        if ($subClass === null) {
            $subClass = self::DEFAULT_CLASS;
        } else {
            if (!is_a($subClass, self::DEFAULT_CLASS, true)) {
                throw new InvalidArgumentException('Invalid subclass ' . $subClass);
            }
        }

        return new $subClass(...self::parseArgs($args));
    }

    protected static function parseArgs(array $args): array
    {
        if (count($args) < 4) {
            throw new InvalidArgumentException("Usage: ${args[0]} <billingId> <clientId> <clientSecret>");
        }

        return [$args[1], $args[2], $args[3]];
    }
}
