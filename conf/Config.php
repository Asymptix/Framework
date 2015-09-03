<?php

namespace conf;

/**
 * Main configuration.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2009 - 2015, Dmytro Zarezenko
 * @license http://opensource.org/licenses/MIT
 */
class Config {
    /**
     * Basic params
     */
    const SITE_TITLE = "Site title";

    /**
     * E-mail configuration
     */
    const EMAIL_ADMIN = "admin@localhost.com";
    const EMAIL_FROM = "support@localhost.com";
    const EMAIL_FROM_NAME = "From name";
    const EMAIL_TPL_FOLDER = "templates/email/";
    const EMAIL_SIGNATURE_TPL = "signature.tpl.php";

    /**
     * Detects if system runned on the local webserver.
     *
     * @return boolean
     */
    public static function isDevServer() {
        return ($_SERVER['SERVER_ADDR'] == '127.0.0.1');
    }

    /**
     * Database configuration.
     * You can add additional configurations associated by hostname as an array
     * key.
     *
     * @var array
     */
    public static $db = array(
        'default' => array(
            'HOST' => "localhost",
            'DBNAME' => "dbname",
            'USER' => "root",
            'PASSWORD' => "pass",
            'DB_CHARSET' => "utf8"
        )
    );

    /**
     * Returns database configuration for current hostname.
     *
     * @return array
     */
    public static function getDBConfig() {
        if (isset(self::$db[$_SERVER['HTTP_HOST']])) {
            return self::$db[$_SERVER['HTTP_HOST']];
        }
        return self::$db['default'];
    }

    /**
     * Returns database configuration parameter.
     *
     * @param string $paramName Parameter name.
     * @return string
     */
    public static function getDBConfigParam($paramName) {
        $dbConfig = self::getDBConfig();
        if (isset($dbConfig) && isset($dbConfig[$paramName])) {
            return $dbConfig[$paramName];
        }
        return "";
    }

    /**
     * FS
     */
    const DIR_UPLOADS = "uploads/";
    const DIR_AVATARS = "uploads/avatars/";

    /**
     * UI
     */
    const ITEMS_PER_PAGE = 15;

    /**
     * You can add you custom parameters here.
     */

}