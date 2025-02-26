<?php

namespace UserModule\Form;

use Laminas\Form\Element\Password;
use Laminas\Form\Element\Submit;
use Laminas\Form\Form;

/**
 * Class LoginForm
 * @package UserModule\Form
 */
class LoginForm extends Form
{
    /** @var string FORM_NAME */
    const FORM_NAME = "loginForm";

    /** @var string TEXT_EMAIL */
    const TEXT_EMAIL = "email";

    /** @var string TEXT_PASSWORD */
    const TEXT_PASSWORD = "password";

    /** @var string TEXT_SUBMIT */
    const TEXT_SUBMIT = "submit";

    /** @var string VALID_EMAIL_PATTERN */
    public const VALID_EMAIL_PATTERN = "^(?:(?!.*?[.]{2})[a-zA-Z0-9](?:[a-zA-Z0-9.+!%-]{1,64}|)|\"[a-zA-Z0-9.+!% -]{1,64}\")@[a-zA-Z0-9][a-zA-Z0-9.-]+(.[a-z]{2,}|.[0-9]{1,})$";

    /**
     * Using Regex i18n code that supports apostrophes, hyphens, and extended ascii
     *
     * @var string VALID_PASSWORD_PATTERN
     */
    const VALID_PASSWORD_PATTERN = "/^[0-9A-Za-z\x{00C0}-\x{00FF}][A-Za-z\x{00C0}-\x{00FF}\'\-]+([\ 0-9A-Za-z\x{00C0}-\x{00FF}][0-9A-Za-z\x{00C0}-\x{00FF}\'\-]+)*/u";

    /**
     * LoginForm constructor.
     * @param null $name
     * @param array $options
     */
    public function __construct($name = null, array $options = [])
    {
        parent::__construct($name, $options);
    }

    /** */
    public function init()
    {
        parent::init();
        $this->setName(self::FORM_NAME);

        $this->add(
            [
                "type" => Password::class,
                "name" => self::TEXT_PASSWORD,
                "options" => [
                    "label" => "Password",
                ],
                "attributes" => [
                    "required" => true,
                    "maxlength" => 40,
                    "pattern" => self::VALID_PASSWORD_PATTERN,
                    "title" => "Enter password",
                    "class" => "form-control",
                ],
            ]
        )->add(
            [
                "type" => Submit::class,
                "name" => self::TEXT_SUBMIT,
                "attributes" => [
                    "value" => "Login",
                    "class" => "btn btn-primary btn-lg btn-block",
                ],
            ]
        );
    }
}