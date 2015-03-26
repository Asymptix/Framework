<?php

/**
 * Common tools methods.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2009 - 2015, Dmytro Zarezenko
 * @license http://opensource.org/licenses/MIT
 */
class Tools {

    /**
     * Check if form was submitted.
     *
     * @return boolean
     */
    public static function isFormSubmitted() {
        return (self::getFieldValue("submitBtn") !== null);
    }

    /**
     * Gets value of the field from $_REQUEST or $_SESSION (is some REQUEST values
     * needs to be stored by scenario).
     *
     * @param mixed $fieldName String name of the field or complex name as array.
     *
     * @return mixed Value of the field, NULL otherwise.
     */
    public static function getFieldValue($fieldName) {
        $fieldName = self::parseComplexFieldName($fieldName);
        $value = null;

        try {
            $value = self::getArrayElement($_REQUEST, $fieldName);
        } catch (Exception $ex) {
            try {
                if (isset($_SESSION['_post'])) {
                    $value = self::getArrayElement($_SESSION['_post'], $fieldName);
                }
            } catch (Exception $ex) {
                return null;
            }
        }

        if (!is_null($value)) {
            if (is_array($value)) {
                return $value;
            } elseif (is_integer($value)) {
                return intval($value);
            }
            return $value;
        }

        return null;
    }

    /**
     * Sets value of the field or creates new field by pair $fieldName => $fieldValue.
     *
     * @global array<mixed> $_FIELDS Global fields list.
     * @param mixed $fieldName Name of the field as a string or complex name as
     *            an array.
     * @param mixed $fieldValue Value of the field.
     */
    public static function setFieldValue($fieldName, $fieldValue) {
        global $_FIELDS;

        $fieldName = self::parseComplexFieldName($fieldName);

        if (!is_array($fieldName)) {
            $_FIELDS[$fieldName] = $fieldValue;
        } else {
            $array = &$_FIELDS;
            for ($i = 0; $i < count($fieldName); $i++) {
                $key = $fieldName[$i];

                if ($i < (count($fieldName) - 1)) {
                    if (!isset($array[$key])) { // declare value as empty array as not last element
                        $array[$key] = array();
                    } else {
                        if (!is_array($array[$key])) { // detect if current value is array because not last element
                            throw new Exception("Try to assign value as array element to the not an array");
                        }
                    }
                    $array = &$array[$key];
                } else { // last element
                    $array[$key] = $fieldValue;
                }
            }
        }
    }

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
     * Remembers field in session.
     *
     * @param string $fieldName Name of the field.
     * @param mixed $fieldValue
     */
    public static function rememberField($fieldName, $fieldValue) {
        $_SESSION['_post'][$fieldName] = serialize($fieldValue);
    }

    public static function forgotField($fieldName) {
        if (isset($_SESSION['_post']) && isset($_SESSION['_post'][$fieldName])) {
            unset($_SESSION['_post'][$fieldName]);
        }
    }

    public static function forgotFields() {
        if (isset($_SESSION['_post'])) {
            unset($_SESSION['_post']);
        }
    }

    /**
     * Change value of the existing field.
     *
     * @global array<mixed> $_FIELDS Global fields list.
     * @param string $fieldName Name of the field.
     * @param mixed $fieldValue Value of the field.
     */
    public static function changeFieldValue($fieldName, $fieldValue) {
        global $_FIELDS;

        if (isset($_FIELDS[$fieldName])) {
            $_FIELDS[$fieldName] = $fieldValue;
        } else {
            throw new Exception("No field '" . $fieldName . "' in global fields list.");
        }
    }

    /**
     * Cast value if the existing field to specified type.
     *
     * @global array<mixed> $_FIELDS Global fields list.
     * @param string $fieldName Name of the field.
     * @param string $type New field value type.
     */
    public static function castFieldValue($fieldName, $type) {
        global $_FIELDS;

        if (isset($_FIELDS[$fieldName])) {
            switch ($type) {
                case ('integer'):
                case ('int'):
                case ('i'):
                    $_FIELDS[$fieldName] = intval($_FIELDS[$fieldName]);
                    break;
                case ('real'):
                case ('float'):
                case ('double'):
                case ('d'):
                    $_FIELDS[$fieldName] = doubleval($_FIELDS[$fieldName]);
                    break;
                case ('string'):
                case ('str'):
                case ('s'):
                    $_FIELDS[$fieldName] = strval($_FIELDS[$fieldName]);
                    break;
                case ('boolean'):
                case ('bool'):
                case ('b'):
                    $_FIELDS[$fieldName] = (boolean) $_FIELDS[$fieldName];
            }
        } else {
            throw new Exception("No field '" . $fieldName . "' in global fields list.");
        }
    }

    /**
     * Normilize all boolean checkboxes even they are not checked.
     *
     * @global array $_FIELDS Submitted form fields.
     * @param array<string> $fieldNames Names of all boolean checkboxes what need
     *           fixes.
     */
    public static function normalizeCheckboxes($fieldNames) {
        global $_FIELDS;

        foreach ($fieldNames as $fieldName) {
            $_FIELDS[$fieldName] = (integer)(boolean)self::getFieldValue($fieldName);
        }
    }

    /**
     * Removes fields from global fields list.
     *
     * @param array<string> $fieldNames Names of all boolean checkboxes what need
     *           fixes (may be list of complex field names).
     */
    public static function removeFields($fieldNames) {
        global $_FIELDS;

        foreach ($fieldNames as $fieldName) {
            $fieldName = self::parseComplexFieldName($fieldName);

            if (!is_array($fieldName)) {
                if (isset($_FIELDS[$fieldName])) {
                    unset($_FIELDS[$fieldName]);
                }
            } else {
                $array = &$_FIELDS;
                for ($i = 0; $i < count($fieldName); $i++) {
                    $key = $fieldName[$i];

                    if ($i < (count($fieldName) - 1)) {
                        if (!isset($array[$key])) {
                            return;
                        }
                        $array = &$array[$key];
                    } else { // last element
                        if (isset($array[$key])) {
                            unset($array[$key]);
                        }
                    }
                }
            }
        }
    }

    /**
     * Check data for Integer.
     *
     * @param mixed $input Data.
     * @return boolean Returns TRUE if $input is a integer, FALSE otherwise.
     */
    public static function isInteger($input) {
        return (ctype_digit(strval($input)));
    }

    /**
     * Check data for double.
     *
     * @param mixed $input Data.
     * @return boolean Returns TRUE if $input is a double, FALSE otherwise.
     */
    public static function isDouble($input) {
        return (is_double($input));
    }

    public static function isDoubleString($input) {
        $doubleValue = (double) $input;
        $stringValue = str_replace(",", ".", (string) $input);

        if (is_numeric($stringValue)) {
            return true;
        }

        if ($stringValue === (string) $doubleValue) {
            return true;
        }
        return false;
    }

    public static function toDouble($value) {
        return (double) str_replace(",", ".", (string) $value);
    }

    /**
     * Finds whether a variable is a number or a numeric string.
     *
     * @param mixed $input The variable being evaluated.
     *
     * @return boolean Returns TRUE if $input is a number or a numeric string, FALSE otherwise.
     */
    public static function isNumeric($input) {
        return (is_numeric(strval($input)));
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
            if (!empty($object)) {
                return true;
            }
            return false;
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

    /**
     * Returns value of the complex array element by it's complex key.
     *
     * @param array $array Complex associated array (dictionary);
     * @param mixed $complexKey List with hierarchy complex key or a value of the
     *            simple (one level) key.
     *
     * @return mixed Value of the array element if found.
     * @throws Exception If can't find element by complex key.
     */
    private static function getArrayElement($array, $complexKey) {
        if (!empty($complexKey)) {
            if (is_array($complexKey)) { // Complex key is provided
                $temp = $array;

                foreach ($complexKey as $key) {
                    if (isset($temp[$key])) {
                        $temp = $temp[$key];
                    } else {
                        throw new Exception("Invalid complex key");
                    }
                }

                return $temp;
            } else { // Simple key is provided
                if (isset($array[$complexKey])) {
                    return $array[$complexKey];
                } else {
                    throw new Exception("Invalid simple key");
                }
            }
        }
        throw new Exception("No array element key provided");
    }

    /**
     * Parse usual HTML notation complex field name into array.
     *
     * @param string  $fieldName Field name.
     *
     * @return mixed String or array.
     */
    private static function parseComplexFieldName($fieldName) {
        $normName = str_replace(
            array('][', '[', ']'),
            array('|', '|', ''),
            $fieldName
        );

        $complexName = explode("|", $normName);
        if (empty($complexName)) {
            return "";
        } elseif (count($complexName) == 1) {
            return $complexName[0];
        }

        //var_dump($complexName);
        return $complexName;
    }

}

?>
