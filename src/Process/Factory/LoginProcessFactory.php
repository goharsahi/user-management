<?php

namespace UserModule\Process\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use UserModule\Form\LoginForm;
use UserModule\Process\LoginProcess;
use UserModule\Request\LoginRequest;

/**
 * Class LoginProcessFactory
 * @package UserModule\Process\Factory
 */
class LoginProcessFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return object|LoginProcess
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): LoginProcess
    {
        return new LoginProcess(
            $container->get(LoginRequest::class),
            $container->get("FormElementManager")
                ->get(LoginForm::class)
        );
    }
}