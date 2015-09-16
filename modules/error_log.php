<?php

/**
 * DB Error Log module.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2015, Dmytro Zarezenko
 *
 * @git https://github.com/Asymptix/Framework
 * @license http://opensource.org/licenses/MIT
 */

register_shutdown_function(function() {
    $error = error_get_last();

    if ($error !== null) {
        \db\tools\ErrorLog::log(
            $error["type"],
            $_SERVER['SCRIPT_NAME'],
            $error["file"],
            (int)$error["line"],
            $error["message"]
        );
    }
});

set_error_handler(function($errno, $msg, $file, $line) {
    db\tools\ErrorLog::log(
        $errno,
        $_SERVER['SCRIPT_NAME'],
        $file,
        $line,
        $msg
    );
}, E_ALL);