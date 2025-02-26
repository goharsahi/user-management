<?php

namespace UserModule\Listener\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use UserModule\Listener\LoginListener;
use UserModule\Service\LoginService;

/**
 * Class LoginListenerFactory
 * @package UserModule\Listener\Factory
 */
class LoginListenerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return object|LoginListener
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        return new LoginListener($container->get(LoginService::class));
    }
}