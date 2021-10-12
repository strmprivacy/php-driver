<?php

namespace Streammachine\Driver\Serializers;

use AvroIOBinaryEncoder;
use AvroIODatumWriter;
use AvroSchema;
use AvroStringIO;
use Streammachine\Driver\Contracts\Event;
use Streammachine\Driver\Contracts\Serializer;

class AvroBinarySerializer implements Serializer
{
    public function serialize(Event $event): string
    {
        $io = new AvroStringIO();
        $writer = new AvroIODatumWriter($event->getStrmSchema());
        $encoder = new AvroIOBinaryEncoder($io);
        $writer->write($event->toArray(), $encoder);

        return $io->string();
    }

    public function getContentType(): string
    {
        return 'application/x-avro-binary';
    }
}
