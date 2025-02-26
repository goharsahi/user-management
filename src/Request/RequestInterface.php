<?php

namespace UserModule\Request;

use UserModule\Response\ResponseAbstract;

/**
 * Class RequestInterface
 * @package UserModule\Request\RequestInterface
 */
interface RequestInterface
{
    /**
     * @return ResponseAbstract
     */
    public function send(): ResponseAbstract;
}
