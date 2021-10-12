<?php

use Examples\Events\DemoEvent;
use Examples\Utils\ClientBuilder;
use Streammachine\Driver\Enums\SerializationType;
use Streammachine\Driver\Sender;

include_once(realpath(dirname(__FILE__)) . '/../../vendor/autoload.php');

/** @var \Streammachine\Driver\Sender $sender */
$sender = ClientBuilder::build($argv, Sender::class);
$event = new DemoEvent();

$event->consentLevels = [0];
$event->producerSessionId = 'producer';
$event->url = 'https://www.google.com';
$event->eventType = 'click';
$event->referrer = 'foo';
$event->userAgent = 'bar';
$event->conversion = 0;
$event->customer = ['id' => 'bla'];
$event->abTests = ['a', 'b'];

$sender->send($event, SerializationType::AVRO_BINARY);
