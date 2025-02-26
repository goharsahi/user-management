<?php

namespace UserModule\Form\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use UserModule\Form\LoginForm;
use UserModule\InputFilter\LoginFormInputFilter;

/**
 * Class LoginFormFactory
 * @package UserModule\Plugin\Factory
 */
class LoginFormFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return object|LoginForm
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): LoginForm
    {
        $loginForm = new LoginForm();
        $loginFormInputFilterManager = $container->get("InputFilterManager");
        $loginFormInputFilter = $loginFormInputFilterManager->get(LoginFormInputFilter::class);
        $loginForm->setInputFilter($loginFormInputFilter);
        $loginForm->setUseAsBaseFieldset(true);

        return $loginForm;
    }
}