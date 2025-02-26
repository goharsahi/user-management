<?php

namespace UserModule\Controller\Factory;

use Laminas\Config\Config;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Session\Container;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use UserModule\Controller\UserController;
use UserModule\Form\EmailForm;
use UserModule\Form\LoginForm;
use UserModule\Service\LoginService;

/**
 * Class UserControllerFactory
 * @package UserModule\Controller\Factory
 */
class UserControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return UserController
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): UserController
    {
        return new UserController(
            new Config($container->get("config")["login_credentials"]),
            $container->get(LoginService::class),
            $container->get("FormElementManager")
                ->get(LoginForm::class),
            $container->get("FormElementManager")
                ->get(EmailForm::class),
            new Container()
        );
    }
}
