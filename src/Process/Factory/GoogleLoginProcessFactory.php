<?php

namespace UserModule\Process\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use UserModule\Process\GoogleLoginProcess;
use UserModule\Request\GoogleLoginRequest;

/**
 * Class GoogleLoginProcessFactory
 * @package UserModule\Process\Factory
 */
class GoogleLoginProcessFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return object|GoogleLoginProcess
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): GoogleLoginProcess
    {
        return new GoogleLoginProcess($container->get(GoogleLoginRequest::class));
    }
}