<?php

use Laminas\Session\Config\StandardConfig;
use Laminas\Session\Storage\ArrayStorage;

return [
    "login_credentials" => [
        "login_success_redirect" => "http://localhost",
        "client_id" => "",
    ],
    "rbac" => [
        "GUEST" => [
            "Application.IndexController.index.view",
            "UserModule.UserController.login.view",
            "UserModule.UserController.login.post",
        ],
        "USER" => [
            "Application.IndexController.dashboard.view",
            "+GUEST",
        ],
        "ADMIN" => [
            "Application.IndexController.*",
            "+USER",
        ],
    ],
    // NB: The value can be GUEST, USER, ADMIN
    "_fixture" => [
        "loggedInUserRole" => "GUEST",
    ],
    "db" => [
        "driver" => "Pdo",
        "dsn" => "mysql:dbname=test;host=test",
        "username" => "test",
        "password" => "test",
    ],
    "session_config" => [
        "cookie_lifetime" => 60 * 60 * 1, // 1 hour ---> 3600
        "gc_maxlifetime" => 60 * 60 * 24 * 7, // 7 days ---> 604800
        "remember_me_seconds" => 3600,
        "use_cookies" => true,
        "config_class" => StandardConfig::class,
    ],
    "session_storage" => [
        "type" => ArrayStorage::class,
    ],
];
