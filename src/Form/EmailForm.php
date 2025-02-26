<?php

namespace UserModule\Form;

use Laminas\Form\Element\Submit;
use Laminas\Form\Element\Text;
use Laminas\Form\Form;

/**
 * Class EmailForm
 * @package UserModule\Form
 */
class EmailForm extends Form
{
    /** @var string FORM_NAME */
    const FORM_NAME = "emailForm";

    /** @var string TEXT_EMAIL */
    const TEXT_EMAIL = "email";

    /** @var string TEXT_SUBMIT */
    const TEXT_SUBMIT = "submit";

    /** @var string VALID_EMAIL_PATTERN */
    public const VALID_EMAIL_PATTERN = "^(?:(?!.*?[.]{2})[a-zA-Z0-9](?:[a-zA-Z0-9.+!%-]{1,64}|)|\"[a-zA-Z0-9.+!% -]{1,64}\")@[a-zA-Z0-9][a-zA-Z0-9.-]+(.[a-z]{2,}|.[0-9]{1,})$";

    /**
     * EmailForm constructor.
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
                "type" => Text::class,
                "name" => self::TEXT_EMAIL,
                "options" => [
                    "label" => "Email",
                ],
                "attributes" => [
                    "required" => true,
                    "maxlength" => 255,
                    "pattern" => self::VALID_EMAIL_PATTERN,
                    "placeholder" => "Email should be a valid email address",
                    "title" => "Enter an Email",
                    "class" => "form-control",
                ],
            ]
        )->add(
            [
                "type" => Submit::class,
                "name" => self::TEXT_SUBMIT,
                "attributes" => [
                    "value" => "Continue",
                    "class" => "btn btn-primary btn-lg btn-block",
                ],
            ]
        );
    }
}