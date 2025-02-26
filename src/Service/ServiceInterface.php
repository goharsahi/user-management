<?php

namespace UserModule\Service;

use Laminas\Config\Config;

/**
 * Class ServiceInterface
 * @package UserModule\Service
 */
interface ServiceInterface
{
    /**
     * @return Config
     */
    public function getConfig(): Config;

    /**
     * @param Config $config
     */
    public function setConfig(Config $config);
}