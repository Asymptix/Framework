<?php

require_once("core/db/beans/Settings.php");

$dbSelector = new DBSelector(new Settings());
$settings = $dbSelector->selectDBObjects();

$_SETTINGS = array();
foreach ($settings as $setting) {
    $_SETTINGS[$setting->id] = $setting->value;
}

?>