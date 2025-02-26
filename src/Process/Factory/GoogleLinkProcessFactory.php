<?php

namespace UserModule\Process\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use UserModule\Process\GoogleLinkProcess;
use UserModule\Request\GoogleLinkRequest;

/**
 * Class GoogleLinkProcessFactory
 * @package UserModule\Process\Factory
 */
class GoogleLinkProcessFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return object|GoogleLinkProcess
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): GoogleLinkProcess
    {
        return new GoogleLinkProcess($container->get(GoogleLinkRequest::class));
    }
}