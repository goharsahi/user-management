<?php

namespace UserModule\Response;

/**
 * Class ResponseInterface
 * @package UserModule\Response\ResponseInterface
 */
interface ResponseInterface
{
    /**
     * @return bool
     */
    public function populate(): bool;
}
