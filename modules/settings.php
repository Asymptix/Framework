<?php

/**
 * Settings module. Gets system settings and configuration from the database.
 *
 * @category Asymptix PHP Framework
 *
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2009 - 2015, Dmytro Zarezenko
 *
 * @git https://github.com/Asymptix/Framework
 *
 * @license http://opensource.org/licenses/MIT
 */
use Asymptix\db\DBSelector;
use db\Settings;

$dbSelector = new DBSelector(new Settings());
$settings = $dbSelector->selectDBObjects();

$_SETTINGS = [];
foreach ($settings as $setting) {
    $_SETTINGS[$setting->id] = $setting->value;
}
