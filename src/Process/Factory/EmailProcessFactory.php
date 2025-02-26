<?php

namespace UserModule\Process\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use UserModule\Form\EmailForm;
use UserModule\Process\EmailProcess;
use UserModule\Request\EmailRequest;

/**
 * Class EmailProcessFactory
 * @package UserModule\Process\Factory
 */
class EmailProcessFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return object|EmailProcess
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): EmailProcess
    {
        return new EmailProcess(
            $container->get(EmailRequest::class),
            $container->get("FormElementManager")
                ->get(EmailForm::class)
        );
    }
}