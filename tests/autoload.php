<?php

$_PATH = null;
$paths = ["../", "./"];
$isFound = false;
foreach ($paths as $_PATH) {
    if (file_exists($_PATH . "vendor/autoload.php")) {
        require_once($_PATH . "vendor/autoload.php");
        $isFound = true;

        break;
    }
}

if (!$isFound) {
    throw new Exception('Unable to load dependencies');
}

spl_autoload_register(function ($className) {
    global $_PATH;

    if (substr($className, 0, 3) == "PHP") {
        return;
    }

    $path = explode("\\", $className);
    if (in_array($path[0], array("conf"))) {
        $includePath = $_PATH . str_replace("\\", "/", $className . ".php");
    } else {
        $includePath = $_PATH . "classes/" . str_replace("\\", "/", $className . ".php");
    }
    require($includePath);
});

// backward compatibility
if (!class_exists('\PHPUnit\Framework\TestCase') &&
    class_exists('\PHPUnit_Framework_TestCase')) {
    class_alias('\PHPUnit_Framework_TestCase', '\PHPUnit\Framework\TestCase');
}

if (!class_exists('\PHPUnit_Framework_TestCase') &&
    class_exists('\PHPUnit\Framework\TestCase')) {
    class_alias('\PHPUnit\Framework\TestCase', '\PHPUnit_Framework_TestCase');
}
