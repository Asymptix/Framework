<?php

/**
 * Settings module. Gets system settings and configuration from the database.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2009 - 2015, Dmytro Zarezenko
 * @license http://opensource.org/licenses/MIT
 */

require_once("core/db/beans/Settings.php");

$dbSelector = new DBSelector(new Settings());
$settings = $dbSelector->selectDBObjects();

$_SETTINGS = array();
foreach ($settings as $setting) {
    $_SETTINGS[$setting->id] = $setting->value;
}

?>