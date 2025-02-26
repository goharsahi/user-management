<?php

namespace UserModule\Request\Factory;

use GuzzleHttp\Client;
use Laminas\Config\Config;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Session\Container;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use UserModule\Form\EmailForm;
use UserModule\Request\EmailRequest;
use UserModule\Response\EmailResponse;

/**
 * Class EmailRequestFactory
 * @package UserModule\Request\Factory
 */
class EmailRequestFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return object|EmailRequest
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): EmailRequest
    {
        return new EmailRequest(
            $container->get(EmailResponse::class),
            $container->get("FormElementManager")
                ->get(EmailForm::class),
            new Client(),
            new Container(),
            new Config($container->get("config"))
        );
    }
}