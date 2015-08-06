<?php

/**
 * Database connections module.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2009 - 2015, Dmytro Zarezenko
 *
 * @git https://github.com/dzarezenko/Asymptix-PHP-Framework.git
 * @license http://opensource.org/licenses/MIT
 */

use Asymptix\DB\DBCore;

$mysqli = new mysqli(
        Config::getDBConfigParam('HOST'),
        Config::getDBConfigParam('USER'), Config::getDBConfigParam('PASSWORD'),
        Config::getDBConfigParam('DBNAME'));
if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
} else {
    if (!$mysqli->set_charset(Config::getDBConfigParam('DB_CHARSET'))) {
        printf("Error loading character set " . Config::getDBConfigParam('DB_CHARSET') . ": %s\n", $mysqli->error);
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