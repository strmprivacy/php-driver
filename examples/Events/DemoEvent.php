<?php

namespace Examples\Events;

use Streammachine\Driver\Contracts\Event;

class DemoEvent implements Event
{
    public $consentLevels;

    public $producerSessionId;

    public $url;

    public $eventType;

    public $referrer;

    public $userAgent;

    public $conversion;

    public $customer;

    public $abTests;

    public function getStrmSchemaRef(): string
    {
        return 'streammachine/clickstream/1.0.0';
    }

    public function toArray(): array
    {
        return [
            'strmMeta' => [
                'eventContractRef' => $this->getStrmSchemaRef(),
                'nonce' => null,
                'timestamp' => null,
                'keyLink' => null,
                'billingId' => null,
                'consentLevels' => $this->consentLevels,
            ],
            'producerSessionId' => $this->producerSessionId,
            'url' => $this->url,
            'eventType' => $this->eventType,
            'referrer' => $this->referrer,
            'userAgent' => $this->userAgent,
            'conversion' => $this->conversion,
            'customer' => $this->customer,
            'abTests' => $this->abTests,
        ];
    }
}
