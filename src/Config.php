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

    /** @var string $keycloakProtocol */
    protected $keycloakProtocol = 'https';

    /** @var string $keycloakHost */
    public $keycloakHost = 'accounts.strmprivacy.io';

    /** @var string $keycloakAuthEndpoint */
    protected $keycloakAuthEndpoint = '/auth/realms/streams/protocol/openid-connect/token';

    /** @var string $keycloakRefreshEndpoint */
    protected $keycloakRefreshEndpoint = '/auth/realms/streams/protocol/openid-connect/token';

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
        return sprintf('%s://%s/%s', $this->keycloakProtocol, $this->keycloakHost, ltrim($this->keycloakAuthEndpoint, '/'));
    }

    public function getRefreshUri(): string
    {
        return sprintf('%s://%s/%s', $this->keycloakProtocol, $this->keycloakHost, ltrim($this->keycloakRefreshEndpoint, '/'));
    }
}
