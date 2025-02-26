<?php

namespace UserModule;

use Laminas\Session\Config\ConfigInterface;
use Laminas\Session\Service\SessionConfigFactory;
use UserModule\Controller\UserController;
use UserModule\Controller\Factory\UserControllerFactory;
use UserModule\Form\EmailForm;
use UserModule\Form\Factory\EmailFormFactory;
use UserModule\Form\Factory\LoginFormFactory;
use UserModule\Form\LoginForm;
use UserModule\InputFilter\EmailFormInputFilter;
use UserModule\InputFilter\Factory\LoginFormInputFilterFactory;
use UserModule\InputFilter\LoginFormInputFilter;
use UserModule\Listener\Factory\LoginListenerFactory;
use UserModule\Listener\LoginListener;
use UserModule\Process\EmailProcess;
use UserModule\Process\Factory\EmailProcessFactory;
use UserModule\Process\Factory\GoogleLoginProcessFactory;
use UserModule\Process\Factory\GoogleLogoutProcessFactory;
use UserModule\Process\Factory\LoginProcessFactory;
use UserModule\Process\GoogleLoginProcess;
use UserModule\Process\GoogleLogoutProcess;
use UserModule\Process\LoginProcess;
use UserModule\Request\EmailRequest;
use UserModule\Request\Factory\EmailRequestFactory;
use UserModule\Request\Factory\GoogleLoginRequestFactory;
use UserModule\Request\Factory\GoogleLogoutRequestFactory;
use UserModule\Request\Factory\LoginRequestFactory;
use UserModule\Request\GoogleLoginRequest;
use UserModule\Request\GoogleLogoutRequest;
use UserModule\Request\LoginRequest;
use UserModule\Response\EmailResponse;
use UserModule\Response\Factory\ResponseFactory;
use UserModule\Response\GoogleLoginResponse;
use UserModule\Response\GoogleLogoutResponse;
use UserModule\Response\LoginResponse;
use UserModule\Service\GoogleClientService;
use UserModule\Service\Factory\GoogleClientServiceFactory;
use UserModule\Service\Factory\LoginServiceFactory;
use UserModule\Service\LoginService;
use Laminas\Router\Http\Segment;

return [
    "listeners" => [
        LoginListener::class,
    ],
    "router" => [
        "routes" => [
            Module::ROUTE_NAME_MODULE => [
                "type" => Segment::class,
                "options" => [
                    "route" => Module::ROUTE_PREFIX,
                    "defaults" => [
                        "controller" => UserController::class,
                        "action" => "index",
                    ],
                ],
                "may_terminate" => true,
                "child_routes" => [
                    Module::ROUTE_NAME_ACTIONS => [
                        "type" => Segment::class,
                        "options" => [
                            "route" => "/:action[/:id]",
                            "constraints" => [
                                "action" => "[a-zA-Z][a-zA-Z0-9_-]*",
                                "id" => "[0-9]+",
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    "controllers" => [
        "factories" => [
            UserController::class => UserControllerFactory::class,
        ],
    ],
    "service_manager" => [
        "factories" => [
            ConfigInterface::class => SessionConfigFactory::class,
            EmailProcess::class => EmailProcessFactory::class,
            EmailRequest::class => EmailRequestFactory::class,
            EmailResponse::class => ResponseFactory::class,
            LoginService::class => LoginServiceFactory::class,
            LoginProcess::class => LoginProcessFactory::class,
            LoginRequest::class => LoginRequestFactory::class,
            LoginResponse::class => ResponseFactory::class,
            LoginListener::class => LoginListenerFactory::class,
            GoogleClientService::class => GoogleClientServiceFactory::class,
            GoogleLoginProcess::class => GoogleLoginProcessFactory::class,
            GoogleLoginRequest::class => GoogleLoginRequestFactory::class,
            GoogleLoginResponse::class => ResponseFactory::class,
            GoogleLogoutProcess::class => GoogleLogoutProcessFactory::class,
            GoogleLogoutRequest::class => GoogleLogoutRequestFactory::class,
            GoogleLogoutResponse::class => ResponseFactory::class,
        ],
    ],
    "form_elements" => [
        "factories" => [
            LoginForm::class => LoginFormFactory::class,
            EmailForm::class => EmailFormFactory::class,
        ],
    ],
    "input_filters" => [
        "factories" => [
            LoginFormInputFilter::class => LoginFormInputFilterFactory::class,
            EmailFormInputFilter::class => LoginFormInputFilterFactory::class,
        ],
    ],
    "view_helper_config" => [
        "flashmessenger" => [
            "message_open_format" => "<div%s><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button><ul><li>",
            "message_close_string" => "</li></ul></div>",
            "message_separator_string" => "</li><li>",
        ],
    ],
    "view_manager" => [
        "not_found_template" => "error/404",
        "exception_template" => "error/index",
        "template_map" => [
            "layout/layout" => __DIR__ . "/../view/user-module/layout.phtml",
            "error/404" => __DIR__ . "/../view/user-module/error/404.phtml",
            "error/index" => __DIR__ . "/../view/user-module/error/index.phtml",
        ],
        "template_path_stack" => [
            __DIR__ . "/../view",
        ],
    ],
];
