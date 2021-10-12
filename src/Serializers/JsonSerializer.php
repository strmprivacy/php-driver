<?php

namespace Streammachine\Driver\Serializers;

use Streammachine\Driver\Contracts\Event;
use Streammachine\Driver\Contracts\Serializer;

class JsonSerializer implements Serializer
{
    public function serialize(Event $event): string
    {
        return json_encode($event->toArray());
    }

    public function getContentType(): string
    {
        return 'application/json';
    }
}
