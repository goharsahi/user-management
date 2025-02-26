<?php

namespace UserModule\Request;

use Laminas\Config\Config;
use UserModule\Response\ResponseAbstract;

/**
 * Class RequestAbstract
 * @package UserModule\Request\RequestAbstract
 */
abstract class RequestAbstract implements RequestInterface
{
    /** @var Config $config*/
    protected Config $config;

    /**
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * @param Config $config
     * @return GoogleLoginRequest
     */
    public function setConfig(Config $config): self
    {
        $this->config = $config;
        return $this;
    }

    /** @inheritdoc */
    abstract public function send(): ResponseAbstract;
}
