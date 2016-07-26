<?php

/**
 * Autoload module.
 *
 * @category Asymptix PHP Framework
 *
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2015, Dmytro Zarezenko
 *
 * @git https://github.com/Asymptix/Framework
 *
 * @license http://opensource.org/licenses/MIT
 */
$deepness = substr_count($_SERVER['SCRIPT_NAME'], '/') - 1;

$_PATH = $deepness > 0 ? implode('', array_fill(0, $deepness, '../')) : './';

require_once $_PATH.'vendor/autoload.php';
spl_autoload_register(function ($className) {
    global $_PATH;

    $path = explode('\\', $className);
    if (in_array($path[0], ['conf'])) {
        $includePath = $_PATH.str_replace('\\', '/', $className.'.php');
    } else {
        $includePath = $_PATH.'classes/'.str_replace('\\', '/', $className.'.php');
    }
    require_once $includePath;
});

require_once $_PATH.'modules/error_log.php';
