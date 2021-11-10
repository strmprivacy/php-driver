<?php

namespace StrmPrivacy\Driver\Serializers;

use StrmPrivacy\Driver\Contracts\Event;
use StrmPrivacy\Driver\Contracts\Serializer;

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
