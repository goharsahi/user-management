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
use UserModule\Form\LoginForm;
use UserModule\Request\LoginRequest;
use UserModule\Response\LoginResponse;

/**
 * Class LoginRequestFactory
 * @package UserModule\Request\Factory
 */
class LoginRequestFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return object|LoginRequest
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidArgumentException
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): LoginRequest
    {
        return new LoginRequest(
            $container->get(LoginResponse::class),
            $container->get("FormElementManager")
                ->get(LoginForm::class),
            new Client(),
            new Container(),
            new Config($container->get("config"))
        );
    }
}