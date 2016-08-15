<?php

namespace Asymptix\core;

/**
 * Common tools methods.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2009 - 2016, Dmytro Zarezenko
 *
 * @git https://github.com/Asymptix/Framework
 * @license http://opensource.org/licenses/MIT
 */
class Tools {

    /**
     * Returns TRUE if filter exists or FALSE otherwise.
     *
     * @param string $filterName Name of the filter field.
     * @return bool
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

        return isset($_FILTER[$filterName]) ? $_FILTER[$filterName] : $defaultValue;
    }

    /**
     * Checks if value is an integer or integer string.
     *
     * @param mixed $value Data.
     * @return bool Returns TRUE if $input is a integer, FALSE otherwise.
     */
    public static function isInteger($value) {
        if (is_array($value)) {
            return false;
        }

        $strVal = trim(strval($value));
        if (strlen($strVal) && $strVal[0] == '-') {
            return ctype_digit(substr($strVal, 1));
        }

        return ctype_digit($strVal);
    }

    /**
     * Check data for double.
     *
     * @param mixed $value Data.
     * @return bool Returns TRUE if $input is a double value, FALSE otherwise.
     */
    public static function isDouble($value) {
        return is_float($value);
    }

    /**
     * Check data for float.
     *
     * @param mixed $value Data.
     * @return bool Returns TRUE if $input is a float value, FALSE otherwise.
     */
    public static function isFloat($value) {
        return self::isDouble($value);
    }

    /**
     * Verify if some string is string representation of some double value.
     * Decimal point may be `.` and `,`.
     *
     * @param string $value
     * @return bool
     */
    public static function isDoubleString($value) {
        $doubleValue = (float)$value;
        $stringValue = str_replace(",", ".", (string)$value);

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
     * @return float
     */
    public static function toDouble($value) {
        return (float)str_replace(",", ".", (string)$value);
    }

    /**
     * Convert string representation of some double value to float.
     *
     * @param string $value
     * @return float
     */
    public static function toFloat($value) {
        return self::toDouble($value);
    }

    /**
     * Finds whether a variable is a number or a numeric string.
     *
     * @param mixed $value The variable being evaluated.
     *
     * @return bool Returns TRUE if $input is a number or a numeric string,
     *           FALSE otherwise.
     */
    public static function isNumeric($value) {
        return is_numeric(strval($value));
    }

    /**
     * Find whether a variable is a boolean value.
     *
     * @param mixed $value The variable being evaluated.
     *
     * @return bool bool Returns TRUE if $input is a number or a numeric string,
     *           FALSE otherwise.
     */
    public static function isBoolean($value) {
        return is_bool($value);
    }

    /**
     * Find whether a variable is a string value.
     *
     * @param mixed $value The variable being evaluated.
     *
     * @return bool bool Returns TRUE if $input is a number or a numeric string,
     *           FALSE otherwise.
     */
    public static function isString($value) {
        return is_string($value);
    }

    /**
     * Find whether a variable is an object.
     *
     * @param mixed $object The variable being evaluated.
     *
     * @return bool Returns TRUE if $object is an object, FALSE otherwise.
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
     * @return bool
     */
    public static function isInstanceOf(&$object, $className) {
        if (is_object($className)) {
            $className = get_class($className);
        }

        return (self::isObject($object) && ($object instanceof $className));
    }

}
