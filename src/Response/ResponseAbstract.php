<?php

namespace UserModule\Response;

/**
 * Class ResponseAbstract
 * @package UserModule\Response\ResponseAbstract
 */
abstract class ResponseAbstract implements ResponseInterface
{
    /** @inheritdoc */
    abstract public function populate(): bool;
}
