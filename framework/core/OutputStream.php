<?php

namespace Asymptix\core;

/**
 * Class wrapper for PHP output stream.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2015, Dmytro Zarezenko
 *
 * @git https://github.com/Asymptix/Framework
 * @license http://opensource.org/licenses/MIT
 */
class OutputStream {

    /**
     * Message highlight types and colors.
     */
    const MSG_INFO    = "#0000CC";
    const MSG_DEBUG   = "#CC00CC";
    const MSG_SUCCESS = "#009900";
    const MSG_WARNING = "#CC9900";
    const MSG_ERROR   = "#CC0000";

    /**
     * Starts to flush output stream.
     */
    public static function start() {
        while (ob_get_level() > 0) {
            ob_end_flush();
        }
        if (ob_get_length() === false) {
            ob_start();
        }
        print("<pre>");
    }

    /**
     * Outputs of some string without end of line.
     *
     * @param string $str String to output.
     */
    public static function output($str) {
        echo($str);

        ob_flush();
        flush();
    }

    /**
     * Outputs of some string with end of line.
     *
     * @param string $str String to output.
     */
    public static function line($str = "") {
        self::output($str . "\n");
    }

    /**
     * Outputs log string with time label before message or instead of {{time}}
     * label (synonym for outputLog(...)).
     *
     * @param string $str String to output.
     * @param string $format Format of the time label
     *            (optional, default: "\[Y-m-d H:i:s\]").
     * @param integer $time Timestamp, if not passed - current time will be used.
     */
    public static function log($str, $format = "\[Y-m-d H:i:s\]", $time = null) {
        if (is_null($time)) {
            $time = time();
        }

        if (strpos($str, "{{time}}") !== false) {
            $str = str_replace("{{time}}", date($format, $time), $str);
            self::line($str);
        } else {
            self::line(date($format, $time) . " " . $str);
        }
    }

    /**
     * Outputs highlighted log string with time label before message or instead
     * of {{time}} label.
     *
     * @param string $msgType Type of the message constant or custom color string.
     * @param string $str String to output.
     * @param string $format Format of the time label
     *            (optional, default: "\[Y-m-d H:i:s\]").
     * @param integer $time Timestamp, if not passed - current time will be used.
     */
    public static function message($msgType, $str, $format = "\[Y-m-d H:i:s\]", $time = null) {
        if (strpos($str, "{{time}}") === false) {
            $str = "{{time}} " . $str;
        }

        self::log(
            '<b><font color="' . $msgType . '">' . $str . '</font></b>',
            $format,
            $time
        );
    }

    /**
     * Outputs highlighted log string with time label before message or instead
     * of {{time}} label.
     *
     * @param string $msgType Type of the message constant or custom color string.
     * @param string $str String to output.
     * @param string $format Format of the time label
     *            (optional, default: "\[Y-m-d H:i:s\]").
     * @param integer $time Timestamp, if not passed - current time will be used.
     */
    public static function msg($msgType, $str, $format = "\[Y-m-d H:i:s\]", $time = null) {
        self::message($msgType, $str, $format, $time);
    }

    /**
     * Closes output stream flushing.
     */
    public static function close() {
        print("</pre>");
        ob_end_flush();
    }

}