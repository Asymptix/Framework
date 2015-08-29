<?php

$request = $_SERVER['SCRIPT_NAME'];
$deepness = substr_count($request, "/") - 1;

$_PATH = $deepness>0 ? implode("", array_fill(0, $deepness, "../")) : "./";

require_once($_PATH . "vendor/autoload.php");
spl_autoload_register(function ($className) {
    global $_PATH;

    $path = explode("\\", $className);
    if (in_array($path[0], array("conf"))) {
        $includePath = $_PATH . str_replace("\\", "/", $className . ".php");
    } else {
        $includePath = $_PATH . "classes/" . str_replace("\\", "/", $className . ".php");
    }
    require_once($includePath);
});

?>