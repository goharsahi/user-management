<?php

namespace UserModule\InputFilter;

use Laminas\InputFilter\InputFilter;
use Laminas\Validator;

/**
 * Class LoginFormInputFilter
 * @package UserModule\InputFilter
 */
class LoginFormInputFilter extends InputFilter
{
    /** */
    public function init()
    {
        parent::init();

        $this->add(
                [
                    "name" => "password",
                    "required" => true,
                    "validators" => [
                        [
                            "name" => Validator\NotEmpty::class,
                        ],
                        [
                            "name" => Validator\StringLength::class,
                            "options" => [
                                "min" => 8,
                                "messages" => [
                                    Validator\StringLength::TOO_SHORT => "Password must have at least 8 characters",
                                ],
                            ],
                        ],
                    ],
                ]
            );
    }
}