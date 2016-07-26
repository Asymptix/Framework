<?php

/**
 * Database connections module.
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
use Asymptix\db\DBCore;
use conf\Config;

$mysqli = new mysqli(
        Config::getDBConfigParam('HOST'),
        Config::getDBConfigParam('USER'), Config::getDBConfigParam('PASSWORD'),
        Config::getDBConfigParam('DBNAME'));
if ($mysqli->connect_error) {
    die('Connect Error ('.$mysqli->connect_errno.') '.$mysqli->connect_error);
} else {
    if (!$mysqli->set_charset(Config::getDBConfigParam('DB_CHARSET'))) {
        printf('Error loading character set '.Config::getDBConfigParam('DB_CHARSET').": %s\n", $mysqli->error);
    }

    $manager = DBCore::getInstance();

    DBCore::connection($mysqli);
    //$manager->setCurrentConnection('first');
}

// Register a shutdown function which will close DB connection
register_shutdown_function(function () {
    global $manager;

    $conns = $manager->getConnections();
    foreach ($conns as $connResource) {
        $manager->closeConnection($connResource);
    }
});
