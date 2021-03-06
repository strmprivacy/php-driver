<?php

namespace StrmPrivacy\Driver\Contracts;

use AvroSchema;

interface Event
{
    public function getStrmSchemaRef(): string;

    public function getStrmSchema(): AvroSchema;

    public function toArray(): array;
}
