<?php

namespace Streammachine\Driver\Contracts;

interface Serializer
{
    public function serialize(Event $event): string;

    public function getContentType(): string;
}
