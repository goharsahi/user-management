<?php

namespace UserModule\Service;

use Laminas\Config\Config;

/**
 * Class ServiceAbstract
 * @package UserModule\Service
 */
class ServiceAbstract implements ServiceInterface
{
    /** @var Config $config */
    protected Config $config;

    /**
     * ServiceAbstract constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * @param Config $config
     * @return ServiceAbstract
     */
    public function setConfig(Config $config): self
    {
        $this->config = $config;

        return $this;
    }
}