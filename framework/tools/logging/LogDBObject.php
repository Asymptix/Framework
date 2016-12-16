<?php

namespace Asymptix\tools\logging;

use Asymptix\db\DBCore;

/**
 * Simple Log class to store Logger messages into teh database.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2016, Dmytro Zarezenko
 * @license http://opensource.org/licenses/MIT
 */
class LogDBObject extends \Asymptix\db\DBObject
{

    const TABLE_NAME = "log";
    const ID_FIELD_NAME = "";
    protected $fieldsList = [
        'type' => "", // varchar(10), default ''
        'message' => "", // text, not null, default ''
        'count' => 0, // int(6) unsigned, not null, default ''
        'last_seen' => "CURRENT_TIMESTAMP", // datetime, not null, default 'CURRENT_TIMESTAMP'
    ];

    /**
     * Log method, must be in all classes for Logger class functionality.
     *
     * @param string $type Log message type from Logger class.
     * @param string $message Message text.
     * @param int $time Timestamp.
     *
     * @return int Number of affected rows.
     * @throws \Asymptix\db\DBCoreException If some database error occurred.
     */
    public static function log($type, $message, $time = null) {
        if (is_null($time)) {
            $time = time();
        }

        $query = "INSERT INTO " . self::TABLE_NAME . " (type, message, count, last_seen) VALUES (?, ?, 1, ?)
                  ON DUPLICATE KEY UPDATE count = count + 1, last_seen = ?";
        try {
            return DBCore::doUpdateQuery($query, "ssss", [
                $type, $message, date("Y-m-d H:i:s", $time), date("Y-m-d H:i:s", $time)
            ]);
        } catch (\Asymptix\db\DBCoreException $e) {
            print($e->getMessage());
        }
    }

}
