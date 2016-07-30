<?php

namespace Asymptix\tools\logging;

use Asymptix\core\OutputStream;

/**
 * Universal Logging functionality. Supports browser output, file and database
 * logging.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2016, Dmytro Zarezenko
 * @license http://opensource.org/licenses/MIT
 */
class Logger {

    /**
     * Log messages types constants.
     */
    const LOG_INFO    = "info";
    const LOG_DEBUG   = "debug";
    const LOG_SUCCESS = "success";
    const LOG_WARNING = "warning";
    const LOG_ERROR   = "error";

    /**
     * Log messages output directions constants.
     */
    const TO_OUTPUT_STREAM = 0;
    const TO_FILE = 1;
    const TO_DB = 2;

    /**
     * Log messages output direction.
     *
     * @var int
     */
    private $direction = null;

    /**
     * Log file name.
     *
     * @var string
     */
    private $fileName = null;

    /**
     * Log DB object. Object must have log() method for logging functionality
     * support.
     *
     * @var \Asymptix\db\DBObject
     */
    private $dbObject = null;

    /**
     * Initiates Logger setting and starts logging.
     *
     * @param int $direction Output direction.
     * @param mixed $output Output file name od DB object.
     *
     * @throws LoggerException
     */
    public function __construct($direction = null, $output = null) {
        switch ($direction) {
            case (null):
            case (self::TO_OUTPUT_STREAM):
                $this->direction = self::TO_OUTPUT_STREAM;
                break;
            case (self::TO_FILE):
                $this->direction = self::TO_FILE;
                $this->fileName = $output;
                break;
            case (self::TO_DB):
                $this->direction = self::TO_DB;
                $this->dbObject = $output;
                break;
            default:
                throw new LoggerException("Invalid logging output direction type");
        }
        $this->start();
    }

    /**
     * Performs preparation to the logging process.
     *
     * @throws LoggerException
     */
    public function start() {
        switch ($this->direction) {
            case (self::TO_OUTPUT_STREAM):
                OutputStream::start();

                return;
            case (self::TO_FILE):
                if (empty($this->fileName)) {
                    throw new LoggerException("Log file name is invalid");
                }
                if (file_exists($this->fileName)) {
                    if (!is_file($this->fileName)) {
                        throw new LoggerException("Log file name is invalid");
                    }
                }
                if (file_put_contents($this->fileName, "", FILE_APPEND) === false) {
                    throw new LoggerException("Can't write to a file");
                }

                return;
            case (self::TO_DB):
                if (empty($this->dbObject) || !is_object($this->dbObject)) {
                    throw new LoggerException("Invalid LogDBObject object");
                }
                if (!method_exists($this->dbObject, "log")) {
                    throw new LoggerException("No log() method in the LogDBObject object");
                }

                return;
            default:
                throw new LoggerException("Invalid logging output direction type");
        }
    }

    /**
     * Main logging method, performes log writing.
     *
     * @param int $type Log message type.
     * @param string $message Message text.
     * @param string $format Time label format.
     * @param int $time Timestamp.
     *
     * @throws LoggerException
     */
    public function log($type, $message, $format = "\[Y-m-d H:i:s\]", $time = null) {
        $msgType = null;
        switch ($type) {
            case (self::LOG_INFO):
                $msgType = OutputStream::MSG_INFO;
                break;
            case (self::LOG_DEBUG):
                $msgType = OutputStream::MSG_DEBUG;
                break;
            case (self::LOG_SUCCESS):
                $msgType = OutputStream::MSG_SUCCESS;
                break;
            case (self::LOG_WARNING):
                $msgType = OutputStream::MSG_WARNING;
                break;
            case (self::LOG_ERROR):
                $msgType = OutputStream::MSG_ERROR;
                break;
            default:
                throw new LoggerException("Invalid message type");
        }

        switch ($this->direction) {
            case (self::TO_OUTPUT_STREAM):
                OutputStream::msg($msgType, $message, $format, $time);

                return;
            case (self::TO_FILE):
                $message = "({$type}) " . $message;

                if (strpos($message, "{{time}}") === false) {
                    $message = "{{time}} " . $message;
                }
                if (is_null($time)) {
                    $time = time();
                }
                $message = str_replace("{{time}}", date($format, $time), $message);

                file_put_contents($this->fileName, "{$message}\n", FILE_APPEND);

                return;
            case (self::TO_DB):
                $this->dbObject->log($type, $message, $time = null);

                return;
            default:
                throw new LoggerException("Invalid logging output direction type");
        }
    }

    /**
     * Closes Logger session.
     *
     * @throws LoggerException
     */
    public function close() {
        switch ($this->direction) {
            case (self::TO_OUTPUT_STREAM):
                OutputStream::close();

                return;
            case (self::TO_FILE):
                // nothing to do
                return;
            case (self::TO_DB):
                // nothing to do
                return;
            default:
                throw new LoggerException("Invalid logging output direction type");
        }
    }

}

class LoggerException extends \Exception {}
