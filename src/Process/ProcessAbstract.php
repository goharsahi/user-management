<?php

namespace UserModule\Process;

use UserModule\Request\RequestAbstract;

/**
 * Class ProcessAbstract
 * @package UserModule\Process\ProcessAbstract
 */
abstract class ProcessAbstract implements ProcessInterface
{
    /** @var RequestAbstract $request */
    protected RequestAbstract $request;

    /**
     * ProcessAbstract constructor.
     * @param RequestAbstract $request
     */
    public function __construct(RequestAbstract $request)
    {
        $this->request = $request;
    }

    /** @inheritdoc */
    abstract public function execute();

    /**
     * @return RequestAbstract
     */
    public function getRequest(): RequestAbstract
    {
        return $this->request;
    }

    /**
     * @param RequestAbstract $request
     * @return ProcessAbstract
     */
    public function setRequest(RequestAbstract $request): ProcessAbstract
    {
        $this->request = $request;

        return $this;
    }
}
