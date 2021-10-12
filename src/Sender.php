<?php

namespace Streammachine\Driver;

use GuzzleHttp\Exception\RequestException;
use Streammachine\Driver\Contracts\Event;
use Streammachine\Driver\Contracts\Serializer;
use Streammachine\Driver\Exceptions\SendingException;
use Streammachine\Driver\Serializers\Factory;

class Sender extends Client
{
    /** @var \Streammachine\Driver\Contracts\Serializer $serializer */
    protected $serializer;

    public function send(Event $event, string $serializationType): void
    {
        $this->initAuthProvider();
        $serializer = $this->getSerializer($serializationType);

        try {
            $response = $this->httpClient->request(
                'POST',
                $this->config->getGatewayUri(),
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->authProvider->getIdToken(),
                        'Strm-Serialization-Type' => $serializer->getContentType(),
                        'Strm-Schema-Ref' => $event->getStrmSchemaRef(),
                        'Content-type' => $serializer->getContentType(),
                    ],
                    'body' => $serializer->serialize($event),
                ]
            );
        } catch (RequestException $e) {
            throw new SendingException(
                sprintf(
                    'Error sending event to %s for billingId %s and clientId %s, status code: %d, message: %s',
                    $this->config->getGatewayUri(),
                    $this->billingId,
                    $this->clientId,
                    $e->getCode(),
                    $e->getMessage(),
                )
            );
        }
    }

    protected function getSerializer(string $serializationType): Serializer
    {
        if (isset($this->serializer)) {
            return $this->serializer;
        }

        return Factory::create($serializationType);
    }
}
