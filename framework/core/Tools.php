<?php

namespace Asymptix\core;

/**
 * Common tools methods.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2009 - 2015, Dmytro Zarezenko
 *
 * @git https://github.com/Asymptix/Framework
 * @license http://opensource.org/licenses/MIT
 */
class Tools {

    /**
     * Returns TRUE if filter exists or FALSE otherwise.
     *
     * @param string $filterName Name of the filter field.
     * @return boolean
     */
    public static function isFilterExists($filterName) {
        return isset($_FILTER[$filterName]);
    }

    /**
     * Returns global filter value if exists.
     *
     * @global array $_FILTER Global filters array.
     * @param string $filterName Name of the filter field.
     * @param mixed $defaultValue Default value if value doesn't exist.
     *
     * @return mixed
     */
    public static function getFilterValue($filterName, $defaultValue = null) {
        global $_FILTER;

        return isset($_FILTER[$filterName])?$_FILTER[$filterName]:$defaultValue;
    }

    /**
     * Check data for Integer.
     *
     * @param mixed $input Data.
     * @return boolean Returns TRUE if $input is a integer, FALSE otherwise.
     */
    public static function isInteger($input) {
        $strVal = trim(strval($input));
        if (strlen($strVal) && $strVal[0] == '-') {
            return ctype_digit(substr($strVal, 1));
        }
        return ctype_digit($strVal);
    }

    /**
     * Check data for double.
     *
     * @param mixed $input Data.
     * @return boolean Returns TRUE if $input is a double, FALSE otherwise.
     */
    public static function isDouble($input) {
        return is_double($input);
    }

    /**
     * Verify if some string is string representation of some double value.
     * Decimal point may be `.` and `,`.
     *
     * @param string $input
     * @return boolean
     */
    public static function isDoubleString($input) {
        $doubleValue = (double)$input;
        $stringValue = str_replace(",", ".", (string)$input);

        if (is_numeric($stringValue)) {
            return true;
        }

        if ($stringValue === (string)$doubleValue) {
            return true;
        }
        return false;
    }

    /**
     * Convert string representation of some double value to double.
     *
     * @param string $value
     * @return double
     */
    public static function toDouble($value) {
        return (double)str_replace(",", ".", (string)$value);
    }

    /**
     * Finds whether a variable is a number or a numeric string.
     *
     * @param mixed $input The variable being evaluated.
     *
     * @return boolean Returns TRUE if $input is a number or a numeric string, FALSE otherwise.
     */
    public static function isNumeric($input) {
        return is_numeric(strval($input));
    }

    public static function isBoolean($input) {
        return is_bool($input);
    }

    public static function isString($input) {
        return is_string($input);
    }

    /**
     * Find whether a variable is an object.
     *
     * @param mixed $object The variable being evaluated.
     *
     * @return boolean Returns TRUE if $object is an object, FALSE otherwise.
     */
    public static function isObject(&$object) {
        if (isset($object) && is_object($object)) {
            return !empty($object);
        }
        return false;
    }

    /**
     * Find whether a $object is an instance of class $className.
     *
     * @param mixed $object The object(variable) being evaluated.
     * @param string $className The name of the class.
     *
     * @return boolean
     */
    public static function isInstanceOf(&$object, $className) {
        if (is_object($className)) {
            $className = get_class($className);
        }
        return (self::isObject($object) && ($object instanceof $className));
    }

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