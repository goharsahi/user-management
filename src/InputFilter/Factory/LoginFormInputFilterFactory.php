<?php

namespace UserModule\InputFilter\Factory;

use Laminas\InputFilter\InputFilter;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use UserModule\InputFilter\LoginFormInputFilter;

/**
 * Class LoginFormInputFilterFactory
 * @package UserModule\InputFilter\Factory
 */
class LoginFormInputFilterFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return object|LoginFormInputFilter
     */
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        ?array $options = null
    ): InputFilter {
        return new $requestedName();
    }
}