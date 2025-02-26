<?php

namespace UserModule\Process;

use UserModule\Form\EmailForm;
use UserModule\Request\EmailRequest;
use UserModule\Response\EmailResponse;

/**
 * Class EmailProcess
 * @package UserModule\Process
 */
class EmailProcess extends ProcessAbstract
{
    /** @var EmailRequest $emailRequest */
    protected EmailRequest $emailRequest;

    /** @var EmailForm $emailForm */
    protected EmailForm $emailForm;

    /**
     * EmailProcess constructor.
     *
     * @param EmailRequest $emailRequest
     * @param EmailForm $emailForm
     */
    public function __construct(EmailRequest $emailRequest, EmailForm $emailForm)
    {
        $this->emailRequest = $emailRequest;

        parent::__construct($emailRequest);
        $this->emailForm = $emailForm;
    }

    /**
     * @return EmailResponse
     */
    public function execute(): EmailResponse
    {
        $this->getEmailRequest()->setEmailForm($this->getEmailForm());

        return $this->getEmailRequest()->send();
    }

    /**
     * @return EmailRequest
     */
    public function getEmailRequest(): EmailRequest
    {
        return $this->emailRequest;
    }

    /**
     * @param EmailRequest $emailRequest
     */
    public function setEmailRequest(EmailRequest $emailRequest): void
    {
        $this->emailRequest = $emailRequest;
    }

    /**
     * @return EmailForm
     */
    public function getEmailForm(): EmailForm
    {
        return $this->emailForm;
    }

    /**
     * @param EmailForm $emailForm
     * @return EmailProcess
     */
    public function setEmailForm(EmailForm $emailForm): self
    {
        $this->emailForm = $emailForm;

        return $this;
    }
}