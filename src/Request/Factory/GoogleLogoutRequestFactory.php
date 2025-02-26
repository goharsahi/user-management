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
use UserModule\Request\GoogleLogoutRequest;
use UserModule\Response\GoogleLogoutResponse;
use UserModule\Service\GoogleClientService;

/**
 * Class GoogleLogoutRequestFactory
 * @package UserModule\Request\Factory
 */
class GoogleLogoutRequestFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return object|GoogleLogoutRequest
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidArgumentException
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): GoogleLogoutRequest
    {
        return new GoogleLogoutRequest(
            $container->get(GoogleClientService::class),
            $container->get(GoogleLogoutResponse::class),
            new Container(),
            new Client(),
            new Config($container->get("config"))
        );
    }
}