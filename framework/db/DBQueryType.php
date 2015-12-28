<?php

namespace Asymptix\db;

/**
 * DB SQL query type class.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2015, Dmytro Zarezenko
 *
 * @git https://github.com/Asymptix/Framework
 * @license http://opensource.org/licenses/MIT
 */
class DBQueryType {

    const SELECT = 'SELECT';
    const INSERT = 'INSERT';
    const UPDATE = 'UPDATE';
    const DELETE = 'DELETE';

    const DESCRIBE = 'DESCRIBE';
    const SHOW = 'SHOW';
    const TRUNCATE = 'TRUNCATE';

    /**
     * Validates SQL query type value.
     *
     * @param string $type SQL query type to validate.
     * @return boolean
     */
    public static function isValidQueryType($type) {
        return in_array($type, self::getQueryTypes());
    }

    /**
     * Returns DBQuery types array from DBQuery types constants list.
     *
     * @return array DBQuery types array.
     */
    public static function getQueryTypes() {
        $oClass = new \ReflectionClass(new \Asymptix\db\DBQueryType);
        $constantsList = $oClass->getConstants();

        return array_keys($constantsList);
    }

    /**
     * Detects type of the SQL query.
     *
     * @param string $query SQL query or query template.
     *
     * @return string Type of the SQL query.
     * @throws DBCoreException If SQL query is invalid.
     */
    public static function detectQueryType($query) {
        $chunks = explode(" ", trim($query));
        if (!isset($chunks[0])) {
            throw new DBQueryTypeException("Invalid SQL query format (can't detect query type)");
        } else {
            $type = strtoupper($chunks[0]);

            if (!self::isValidQueryType($type)) {
                throw new DBQueryTypeException("Invalid SQL query type '" . $type . "'");
            }

            return $type;
        }
    }

}

/**
 * Service exception class.
 */
class DBQueryTypeException extends \Exception {}