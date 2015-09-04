<?php

$_PATH = "../";

require_once($_PATH . "vendor/autoload.php");
spl_autoload_register(function ($className) {
    global $_PATH;

    if (substr($className, 0, 3) == "PHP") return;

    $path = explode("\\", $className);
    if (in_array($path[0], array("conf"))) {
        $includePath = $_PATH . str_replace("\\", "/", $className . ".php");
    } else {
        $includePath = $_PATH . "classes/" . str_replace("\\", "/", $className . ".php");
    }
    require_once($includePath);
});

?>