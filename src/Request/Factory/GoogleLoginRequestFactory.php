<?php

namespace UserModule\Request\Factory;

use GuzzleHttp\Client;
use Laminas\Config\Config;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Session\Container;
use Laminas\Session\Exception\InvalidArgumentException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use UserModule\Request\GoogleLoginRequest;
use UserModule\Response\GoogleLoginResponse;
use UserModule\Service\GoogleClientService;

/**
 * Class GoogleLoginRequestFactory
 * @package UserModule\Request\Factory
 */
class GoogleLoginRequestFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return object|GoogleLoginRequest
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidArgumentException
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): GoogleLoginRequest
    {
        return new GoogleLoginRequest(
            $container->get(GoogleClientService::class),
            $container->get(GoogleLoginResponse::class),
            new Container(),
            new Client(),
            new Config($container->get("config"))
        );
    }
}