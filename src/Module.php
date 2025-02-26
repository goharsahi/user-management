<?php

namespace UserModule;

/**
 * Class Module
 * @package UserModule
 */
class Module
{
    public const VERSION = "0.0.2";
    public const MODULE_NAME = "UserModule";
    public const USER_CONFIG = "user-config";

    public const ROUTE_PREFIX = "/user";

    public const ROUTE_NAME_MODULE = "user";
    public const ROUTE_NAME_ACTIONS = "actions";
    public const ROUTE_MODULE_CHILD_ACTIONS = "user/actions";

    /** @var string MESSAGE_NOT_ALLOWED */
    const MESSAGE_NOT_ALLOWED = "You are not allowed to log in.";

    /** @var string MESSAGE_LOGGED_IN */
    const MESSAGE_LOGGED_IN = "You are now logged in.";

    /** @return array */
    public function getConfig(): array
    {
        return include __DIR__ . "/../config/module.config.php";
    }
}
