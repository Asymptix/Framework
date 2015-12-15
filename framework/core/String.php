<?php

namespace Asymptix\core;

/**
 * Strings connected methods.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2015, Dmytro Zarezenko
 *
 * @git https://github.com/Asymptix/Framework
 * @license http://opensource.org/licenses/MIT
 */
class String {

    /**
     * Returns a string with the first character of str capitalized, if that
     * character is alphabetic.
     *
     * @param string The input string.
     * @param string $encoding The encoding parameter is the character encoding.
     *            If it is omitted, the internal character encoding value will
     *            be used.
     *
     * @return string Returns the resulting string.
     */
    public static function upperCaseFirst($str, $encoding = 'utf8') {
        if ($encoding) {
            $firstLetter = mb_substr(mb_strtoupper($str, $encoding), 0, 1, $encoding);
            return ($firstLetter . mb_substr($str, 1, null, $encoding));
        } else {
            $firstLetter = mb_substr(mb_strtoupper($str), 0, 1);
            return ($firstLetter . mb_substr($str, 1));
        }
    }

    /**
     * Returns a string with the first character of str lowercased, if that
     * character is alphabetic.
     *
     * @param string The input string.
     * @param string $encoding The encoding parameter is the character encoding.
     *            If it is omitted, the internal character encoding value will
     *            be used.
     *
     * @return string Returns the resulting string.
     */
    public static function lowerCaseFirst($str, $encoding = 'utf8') {
        if ($encoding) {
            $firstLetter = mb_substr(mb_strtoupper($str, $encoding), 0, 1, $encoding);
            return ($firstLetter . mb_substr($str, 1, null, $encoding));
        } else {
            $firstLetter = mb_substr(mb_strtoupper($str), 0, 1);
            return ($firstLetter . mb_substr($str, 1));
        }
    }

    /**
     * Returns a string with the first character of each word in str capitalized,
     * if that character is alphabetic.
     *
     * @param string The input string.
     * @param string $encoding The encoding parameter is the character encoding.
     *            If it is omitted, the internal character encoding value will
     *            be used.
     *
     * @return string Returns the resulting string.
     */
    public static function upperCaseWords($str, $encoding = 'utf8') {
        if ($encoding) {
            return mb_convert_case($str, MB_CASE_TITLE, $encoding);
        } else {
            return mb_convert_case($str, MB_CASE_TITLE);
        }
    }

    /**
     * Returns string with all alphabetic characters converted to uppercase.
     *
     * @param string The input string.
     * @param string $encoding The encoding parameter is the character encoding.
     *            If it is omitted, the internal character encoding value will
     *            be used.
     *
     * @return string Returns the resulting string.
     */
    public static function upperCase($str, $encoding = 'utf8') {
        if ($encoding) {
            return mb_strtoupper($str, $encoding);
        } else {
            return mb_strtoupper($str);
        }
    }

    /**
     * Returns string with all alphabetic characters converted to lowercase.
     *
     * @param string The input string.
     * @param string $encoding The encoding parameter is the character encoding.
     *            If it is omitted, the internal character encoding value will
     *            be used.
     *
     * @return string Returns the resulting string.
     */
    public static function lowerCase($str, $encoding = 'utf8') {
        if ($encoding) {
            return mb_strtolower($str, $encoding);
        } else {
            return mb_strtolower($str);
        }
    }

}