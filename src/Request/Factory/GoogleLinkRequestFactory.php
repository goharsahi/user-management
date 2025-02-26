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
use UserModule\Request\GoogleLinkRequest;
use UserModule\Response\GoogleLinkResponse;
use UserModule\Service\GoogleClientService;

/**
 * Class GoogleLinkRequestFactory
 * @package UserModule\Request\Factory
 */
class GoogleLinkRequestFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return object|GoogleLinkRequest
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidArgumentException
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): GoogleLinkRequest
    {
        return new GoogleLinkRequest(
            $container->get(GoogleClientService::class),
            $container->get(GoogleLinkResponse::class),
            new Container(),
            new Client(),
            new Config($container->get("config"))
        );
    }
}