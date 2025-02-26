<?php

namespace UserModule\Repository\Factory;

use Laminas\Db\Adapter\Adapter;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use UserModule\Repository\RepositoryAbstract;

/**
 * Class RepositoryFactory
 * @package UserModule\Repository\Factory
 */
class RepositoryFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return RepositoryAbstract
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): RepositoryAbstract
    {
        return new $requestedName($container->get(Adapter::class));
    }
}
