<?php

/**
 * Database connections module.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2009 - 2015, Dmytro Zarezenko
 * @license http://opensource.org/licenses/MIT
 */

$mysqli = new mysqli(
        Config::getDBConfigParam('HOST'),
        Config::getDBConfigParam('USER'), Config::getDBConfigParam('PASSWORD'),
        Config::getDBConfigParam('DBNAME'));
if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
} else {
    //change character set to utf8
    if (!$mysqli->set_charset(Config::DB_CHARSET)) {
        printf("Error loading character set " . Config::DB_CHARSET . ": %s\n", $mysqli->error);
    } else {
        //printf("Current character set: %s\n", $mysqli->character_set_name());
    }

    $manager = DBCore::getInstance();

    DBCore::connection($mysqli);
    //$manager->setCurrentConnection('first');
}

// Register a shutdown function which will close DB connection
function onExit() {
    global $manager;
    $conns = $manager->getConnections();
    foreach ($conns as $connName => $connResource) { // TODO: without name
        $manager->closeConnection($connResource);
    }
}
register_shutdown_function("onExit");


?>