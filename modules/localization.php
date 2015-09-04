<?php

/**
 * Works with language in session and POST requests (to set language).
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2009 - 2015, Dmytro Zarezenko
 *
 * @git https://github.com/Asymptix/Framework
 * @license http://opensource.org/licenses/MIT
 */

use Asymptix\localization\Languages;

if (isset($_POST['lang'])) {
    $_SESSION['lang'] = $_POST['lang'];
} elseif (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'en';
}

$_LANG = Languages::getLanguage($_SESSION['lang']);
$_SESSION['lang'] = $_LANG->code;

require_once(realpath(dirname(__FILE__)) . "/../conf/langs/" . $_LANG->code . ".php");