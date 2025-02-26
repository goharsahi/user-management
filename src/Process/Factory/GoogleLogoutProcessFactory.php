<?php

namespace UserModule\Process\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use UserModule\Process\GoogleLogoutProcess;
use UserModule\Request\GoogleLogoutRequest;

/**
 * Class GoogleLogoutProcessFactory
 * @package UserModule\Process\Factory
 */
class GoogleLogoutProcessFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return object|GoogleLogoutProcess
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): GoogleLogoutProcess
    {
        return new GoogleLogoutProcess($container->get(GoogleLogoutRequest::class));
    }
}