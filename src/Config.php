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

    /** @var string $stsProtocol */
    protected $stsProtocol = 'https';

    /** @var string $stsHost */
    protected $stsHost = 'sts.strmprivacy.io';

    /** @var string $stsAuthEndpoint */
    protected $stsAuthEndpoint = '/auth';

    /** @var string $stsRefreshEndpoint */
    protected $stsRefreshEndpoint = '/refresh';

    /** @var int $stsRefreshInterval */
    protected $stsRefreshInterval = 3300;

    public function __construct(array $config = [])
    {
        foreach ($config as $name => $value) {
            if (isset($this->{$name})) {
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
        return sprintf('%s://%s/%s', $this->stsProtocol, $this->stsHost, ltrim($this->stsAuthEndpoint, '/'));
    }

    public function getRefreshUri(): string
    {
        return sprintf('%s://%s/%s', $this->stsProtocol, $this->stsHost, ltrim($this->stsRefreshEndpoint, '/'));
    }
}
