<?php

namespace StrmPrivacy\Driver\Serializers;

use AvroIOBinaryEncoder;
use AvroIODatumWriter;
use AvroSchema;
use AvroStringIO;
use StrmPrivacy\Driver\Contracts\Event;
use StrmPrivacy\Driver\Contracts\Serializer;

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
