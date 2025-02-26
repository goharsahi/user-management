<?php

namespace UserModule\Service\Factory;

use Google_Client;
use Laminas\Session\Container;
use Laminas\Session\Exception\InvalidArgumentException;
use Psr\Container\ContainerInterface;
use UserModule\Service\GoogleClientService;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class GoogleClientServiceFactory
 * @package UserModule\Service\Factory
 */
class GoogleClientServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return object|GoogleClientService
     * @throws InvalidArgumentException
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): GoogleClientService
    {
        return new GoogleClientService(
            new Google_Client(),
            new Container()
        );
    }
}