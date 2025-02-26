<?php

namespace UserModule\Response\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use UserModule\Response\ResponseAbstract;

/**
 * Class ResponseFactory
 * @package UserModule\Response\Factory
 */
class ResponseFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ResponseAbstract
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): ResponseAbstract
    {
        return new $requestedName();
    }
}