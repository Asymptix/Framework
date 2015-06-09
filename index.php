<?php

session_start();

require_once("conf/Config.php");

/**
 * Multilanguage functionality
 */
require_once("core/localisation/Languages.php");
require_once("conf/langs/" . $_LANG->code . "/Language.php");

require_once("core/Tools.php");
require_once("core/Errors.php");
require_once("core/Messages.php");
require_once("core/db/DBCore.php");

require_once("core/Route.php");

include("modules/dbconnection.module.php");
include("modules/settings.module.php");
include("modules/session.module.php");
include("modules/filter.module.php");

/**
 * Fetching request parameters
 */
$request = $_SERVER['REQUEST_URI'];

/**
 * Fetch arguments of the request and separate request string from arguments.
 */
$_ARGS = array();
$qmPosition = strrpos($request, "?");
if ($qmPosition !== false) {
    $argumentsStr = substr($request, $qmPosition + 1);
    foreach (explode("&", $argumentsStr) as $argPairStr) {
        list($argumentName, $argumentValue) = explode("=", $argPairStr);

        $_ARGS[$argumentName] = $argumentValue;
    }

    $request = substr($request, 0, $qmPosition);
}

/**
 * Pagination
 */
if (isset($_ARGS['pn'])) {
    $_REQUEST['pn'] = (integer)$_ARGS['pn'];
}

$_ROUTE = new Route(array_filter(explode("/", $request)));
if (empty($_ROUTE->controller)) {
    $_ROUTE->controller = "home";
    if (User::checkLoggedIn()) {
        $_ROUTE->controller = "dashboard";
    }
}
/**
 * Switches to the needed control
 */
$isBackend = !in_array($_ROUTE->controller, array(
    'home',
    'login', 'logout', 'forgot-password', 'sign-up',
    'pricing', 'contact-us'
));

if ($isBackend && !User::checkLoggedIn()) {
    $isBackend = false;
    $_ROUTE->controller = "login";
}
if ($_ROUTE->controller == "login" && User::checkLoggedIn()) {
    $isBackend = true;
    $_ROUTE->controller = "dashboard";
}

if ($isBackend) {
    require_once("controllers/backend/" . $_ROUTE->controller . ".php");
    require_once("templates/backend/master.tpl.php");
} else {
    require_once("controllers/frontend/" . $_ROUTE->controller . ".php");
    require_once("templates/frontend/master.tpl.php");
}

?>