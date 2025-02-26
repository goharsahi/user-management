<?php

namespace UserModule\Form\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use UserModule\Form\EmailForm;
use UserModule\InputFilter\EmailFormInputFilter;

/**
 * Class EmailFormFactory
 * @package UserModule\Plugin\Factory
 */
class EmailFormFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return object|EmailForm
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): EmailForm
    {
        $loginForm = new EmailForm();
        $loginFormInputFilterManager = $container->get("InputFilterManager");
        $loginFormInputFilter = $loginFormInputFilterManager->get(EmailFormInputFilter::class);
        $loginForm->setInputFilter($loginFormInputFilter);
        $loginForm->setUseAsBaseFieldset(true);

        return $loginForm;
    }
}