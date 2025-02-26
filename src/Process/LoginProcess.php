<?php

namespace UserModule\Process;

use UserModule\Form\LoginForm;
use UserModule\Request\LoginRequest;
use UserModule\Response\LoginResponse;

/**
 * Class LoginProcess
 * @package UserModule\Process
 */
class LoginProcess extends ProcessAbstract
{
    /** @var LoginRequest $loginRequest */
    protected LoginRequest $loginRequest;

    /** @var LoginForm $loginForm */
    protected LoginForm $loginForm;

    /**
     * LoginProcess constructor.
     * @param LoginRequest $loginRequest
     * @param LoginForm $loginForm
     */
    public function __construct(LoginRequest $loginRequest, LoginForm $loginForm)
    {
        $this->loginRequest = $loginRequest;

        parent::__construct($loginRequest);
        $this->loginForm = $loginForm;
    }

    /**
     * @return LoginResponse
     */
    public function execute(): LoginResponse
    {
        $this->getLoginRequest()->setLoginForm($this->getLoginForm());

        return $this->getLoginRequest()->send();
    }

    /**
     * @return LoginRequest
     */
    public function getLoginRequest(): LoginRequest
    {
        return $this->loginRequest;
    }

    /**
     * @param LoginRequest $loginRequest
     */
    public function setLoginRequest(LoginRequest $loginRequest): void
    {
        $this->loginRequest = $loginRequest;
    }

    /**
     * @return LoginForm
     */
    public function getLoginForm(): LoginForm
    {
        return $this->loginForm;
    }

    /**
     * @param LoginForm $loginForm
     * @return LoginProcess
     */
    public function setLoginForm(LoginForm $loginForm): self
    {
        $this->loginForm = $loginForm;

        return $this;
    }
}