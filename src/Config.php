<?php

namespace StrmPrivacy\Driver;

class Config
{
    /** @var string $gatewayProtocol */
    protected $gatewayProtocol = 'https';

    /** @var string $gatewayHost */
    protected $gatewayHost = 'events.strmprivacy.io';

    /** @var string $gatewayEndpoint */
    protected $gatewayEndpoint = '/event';

    /** @var string $authProtocol */
    protected $authProtocol = 'https';

    /** @var string $authHost */
    public $authHost = 'accounts.strmprivacy.io';

    /** @var string $authAuthEndpoint */
    protected $authAuthEndpoint = '/auth/realms/streams/protocol/openid-connect/token';

    /** @var string $authRefreshEndpoint */
    protected $authRefreshEndpoint = '/auth/realms/streams/protocol/openid-connect/token';

    /** @var int $stsRefreshInterval */
    protected $stsRefreshInterval = 3300;

    public function __construct(array $config = [])
    {
        foreach ($config as $name => $value) {
            if (isset($this->{$name}) && isset($value)) {
                $this->{$name} = $value;
            }
        }
    }

    public function getGatewayUri(): string
    {
        return sprintf('%s://%s/%s', $this->gatewayProtocol, $this->gatewayHost, ltrim($this->gatewayEndpoint, '/'));
    }

    public function getAuthUri(): string
    {
        return sprintf('%s://%s/%s', $this->authProtocol, $this->authHost, ltrim($this->authAuthEndpoint, '/'));
    }

    public function getRefreshUri(): string
    {
        return sprintf('%s://%s/%s', $this->authProtocol, $this->authHost, ltrim($this->authRefreshEndpoint, '/'));
    }
}
