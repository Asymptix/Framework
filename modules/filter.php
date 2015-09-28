<?php

/**
 * UI data filters functionality.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2009 - 2015, Dmytro Zarezenko
 *
 * @git https://github.com/Asymptix/Framework
 * @license http://opensource.org/licenses/MIT
 */

use Asymptix\web\Request;

// Set and Reset filter submit fields names may be changed.
$setFilter = (boolean)Request::getFieldValue('setFilter');
$resetFilter = (boolean)Request::getFieldValue('resetFilter');

if (isset($_SESSION['_filter'])) {
    $_FILTER = unserialize($_SESSION['_filter']);
} else {
    $_FILTER = array();
}

if ($setFilter) {
    $_FILTER = array_merge($_FILTER, $_REQUEST);
    unset($_FILTER['setFilter']);
}
if ($resetFilter) {
    $_FILTER = array();
}

$_SESSION['_filter'] = serialize($_FILTER);