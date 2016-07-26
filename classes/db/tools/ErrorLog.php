<?php

namespace db\tools;

use Asymptix\db\DBCore;

/**
 * Error Log class to store PHP errors into database and classify them by type,
 * script and line of code.
 *
 * @category Asymptix PHP Framework
 *
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2009 - 2016, Dmytro Zarezenko
 * @license http://opensource.org/licenses/MIT
 */
class ErrorLog extends \Asymptix\db\DBObject
{
    const TABLE_NAME = 'log_errors';
    const ID_FIELD_NAME = '';
    protected $fieldsList = [
        'type'       => 0, // int(2) unsigned, not null, default ''
        'error_type' => '', // varchar(100), not null, default ''
        'called'     => '', // varchar(1000), not null, default ''
        'script'     => '', // varchar(255), not null, default ''
        'line'       => 0, // int(5) unsigned, not null, default ''
        'message'    => '', // text, not null, default ''
        'count'      => 0, // int(6) unsigned, not null, default ''
        'last_seen'  => 'CURRENT_TIMESTAMP', // datetime, not null, default 'CURRENT_TIMESTAMP'
    ];

    public static function log($type, $called, $script, $line, $message)
    {
        if (is_null($called)) {
            $called = $_SERVER['SCRIPT_NAME'];
        }

        $errorTypes = [];
        if ($type === 0) {
            $errorTypes[] = 'E_LOG_INFO';
        } else {
            for ($i = 0; $i < 15; $i++) {
                $errorType = self::friendlyErrorType($type & pow(2, $i));
                if (!empty($errorType)) {
                    $errorTypes[] = $errorType;
                }
            }
        }

        $query = 'INSERT INTO '.self::TABLE_NAME.' (type, error_type, called, script, line, message, count, last_seen) VALUES (?, ?, ?, ?, ?, ?, 1, ?)
                  ON DUPLICATE KEY UPDATE count = count + 1, last_seen = ?';
        try {
            return DBCore::doUpdateQuery($query, 'isssisss', [
                $type, implode(', ', $errorTypes), $called, $script, $line, $message, date('Y-m-d H:i:s'), date('Y-m-d H:i:s'),
            ]);
        } catch (\Asymptix\db\DBCoreException $e) {
            echo $e->getMessage();
        }
    }

    private static function friendlyErrorType($type)
    {
        switch ($type) {
            case E_ERROR: // 1
                return 'E_ERROR';
            case E_WARNING: // 2
                return 'E_WARNING';
            case E_PARSE: // 4
                return 'E_PARSE';
            case E_NOTICE: // 8
                return 'E_NOTICE';
            case E_CORE_ERROR: // 16
                return 'E_CORE_ERROR';
            case E_CORE_WARNING: // 32
                return 'E_CORE_WARNING';
            case E_COMPILE_ERROR: // 64
                return 'E_COMPILE_ERROR';
            case E_COMPILE_WARNING: // 128
                return 'E_COMPILE_WARNING';
            case E_USER_ERROR: // 256
                return 'E_USER_ERROR';
            case E_USER_WARNING: // 512
                return 'E_USER_WARNING';
            case E_USER_NOTICE: // 1024
                return 'E_USER_NOTICE';
            case E_STRICT: // 2048
                return 'E_STRICT';
            case E_RECOVERABLE_ERROR: // 4096
                return 'E_RECOVERABLE_ERROR';
            case E_DEPRECATED: // 8192
                return 'E_DEPRECATED';
            case E_USER_DEPRECATED: // 16384
                return 'E_USER_DEPRECATED';
        }

        return '';
    }
}
