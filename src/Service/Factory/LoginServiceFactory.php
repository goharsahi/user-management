<?php

namespace UserModule\Service\Factory;

use Laminas\Config\Config;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Session\Container;
use Laminas\Session\Exception\InvalidArgumentException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use UserModule\Process\EmailProcess;
use UserModule\Process\GoogleLoginProcess;
use UserModule\Process\GoogleLogoutProcess;
use UserModule\Process\LoginProcess;
use UserModule\Service\LoginService;

/**
 * Class LoginServiceFactory
 * @package UserModule\Service\Factory
 */
class LoginServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return object|LoginService
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidArgumentException
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): LoginService
    {
        return new LoginService(
            new Config($container->get("config")),
            $container->get(EmailProcess::class),
            $container->get(LoginProcess::class),
            $container->get(GoogleLoginProcess::class),
            $container->get(GoogleLogoutProcess::class),
            new Container()
        );
    }
}