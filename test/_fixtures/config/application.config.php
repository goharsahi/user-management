<?php

return [
    "modules" => [
        "Laminas\Mvc\Plugin\FlashMessenger",
        "Laminas\Session",
        "Laminas\Form",
        "Laminas\Hydrator",
        "Laminas\InputFilter",
        "Laminas\Db",
        "UserModule",
        "Laminas\Filter",
        "Laminas\Mvc\I18n",
        "Laminas\I18n",
        "Laminas\Router",
        "Laminas\Validator",
        "Laminas\ZendFrameworkBridge",
        "Laminas\ApiTools\ApiProblem",
        "Laminas\ApiTools\ContentNegotiation",
        "Laminas\ApiTools\OAuth2",
    ],
    "module_listener_options" => [
        "use_laminas_loader" => false,
        "config_glob_paths" => [
            realpath(__DIR__) . "/autoload/{{,*.}global,{,*.}local}.php",
        ],
        "config_cache_enabled" => true,
        "config_cache_key" => "application.config.cache",
        "module_map_cache_enabled" => true,
        "module_map_cache_key" => "application.module.cache",
        "cache_dir" => "data/cache/",
    ],
];
