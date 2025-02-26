<?php

namespace UserModule\InputFilter;

use Laminas\Filter;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator;

/**
 * Class EmailFormInputFilter
 * @package UserModule\InputFilter
 */
class EmailFormInputFilter extends InputFilter
{
    /** */
    public function init()
    {
        parent::init();

        $this->add(
            [
                "name" => "email",
                "required" => true,
                "filters" => [
                    ["name" => Filter\StripTags::class],
                    ["name" => Filter\StringTrim::class],
                ],
                "validators" => [
                    ["name" => Validator\NotEmpty::class],
                    ["name" => Validator\EmailAddress::class],
                    [
                        "name" => Validator\StringLength::class,
                        "options" => [
                            "encoding" => "UTF-8",
                            "min" => 6,
                            "max" => 128,
                            "messages" => [
                                Validator\StringLength::TOO_SHORT => "Email address must have at least 6 characters",
                                Validator\StringLength::TOO_LONG => "Email address must have at most 128 characters",
                            ],
                        ],
                    ],
                ],
            ]
        );
    }
}