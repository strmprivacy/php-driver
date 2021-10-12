<?php

namespace Streammachine\Driver\Contracts;

interface Event
{
    public function getStrmSchemaRef(): string;

    public function toArray(): array;
}
