<?php

namespace Asymptix\web;

use Asymptix\web\Http;

/**
 * Request functionality.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2009 - 2016, Dmytro Zarezenko
 *
 * @git https://github.com/Asymptix/Framework
 * @license http://opensource.org/licenses/MIT
 */
class Request {

    /**
     * Check if form was submitted.
     *
     * @param string $submitFieldName Name of the submit field (button).
     *           Default: "submitBtn"
     *
     * @return boolean
     */
    public static function isFormSubmitted($submitFieldName = "submitBtn") {
        return (self::getFieldValue($submitFieldName) !== null);
    }

    /**
     * Verify if field is exists in the request.
     *
     * @param mixed $fieldName String name of the field or complex name as array.
     * @param string $source Http::GET or Http::POST constant.
     *
     * @return bool
     */
    public static function issetField($fieldName, $source = null) {
        return !is_null(self::getFieldValue($fieldName, $source));
    }

    /**
     * Gets value of the field from $_REQUEST or $_SESSION (is some REQUEST values
     * needs to be stored by scenario). Also it takes values from $_GET or $_POST
     * separately if second parameter is passed.
     *
     * @param mixed $fieldName String name of the field or complex name as array.
     * @param string $source Http::GET or Http::POST constant.
     *
     * @return mixed Value of the field, NULL otherwise.
     */
    public static function getFieldValue($fieldName, $source = null) {
        $fieldName = self::parseComplexFieldName($fieldName);
        $value = null;

        try {
            switch ($source) {
                case (Http::GET):
                    $value = self::getArrayElement($_GET, $fieldName);
                    break;
                case (Http::POST):
                    $value = self::getArrayElement($_POST, $fieldName);
                    break;
                default:
                    $value = self::getArrayElement($_REQUEST, $fieldName);
            }
        } catch (\Exception $ex) {
            try {
                if (isset($_SESSION['_post'])) {
                    $value = self::getArrayElement($_SESSION['_post'], $fieldName);
                }
            } catch (\Exception $ex) {
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
     * Returns value of the HTTP GET requet field.
     *
     * @param mixed $fieldName String name of the field or complex name as array.
     *
     * @return mixed Value of the field, NULL otherwise.
     */
    public static function _get($fieldName) {
        return self::getFieldValue($fieldName, Http::GET);
    }

    /**
     * Returns value of the HTTP POST requet field.
     *
     * @param mixed $fieldName String name of the field or complex name as array.
     *
     * @return mixed Value of the field, NULL otherwise.
     */
    public static function _post($fieldName) {
        return self::getFieldValue($fieldName, Http::POST);
    }

    /**
     * Returns value of the HTTP POST or GET requet field.
     *
     * @param mixed $fieldName String name of the field or complex name as array.
     *
     * @return mixed Value of the field, NULL otherwise.
     */
    public static function _field($fieldName) {
        return self::getFieldValue($fieldName);
    }

    /**
     * Sets value of the field or creates new field by pair $fieldName => $fieldValue.
     *
     * @global array<mixed> $_FIELDS Global fields list.
     * @param mixed $fieldName Name of the field as a string or complex name as
     *            an array.
     * @param mixed $fieldValue Value of the field.
     *
     * @throws \Exception
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
                            throw new \Exception("Try to assign value as array element to the not an array");
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
     * Remembers field in session.
     *
     * @param string $fieldName Name of the field.
     * @param mixed $fieldValue
     */
    public static function rememberField($fieldName, $fieldValue) {
        $_SESSION['_post'][$fieldName] = serialize($fieldValue);
    }

    /**
     * TODO: add docs
     *
     * @param type $fieldName
     */
    public static function forgotField($fieldName) {
        if (isset($_SESSION['_post']) && isset($_SESSION['_post'][$fieldName])) {
            unset($_SESSION['_post'][$fieldName]);
        }
    }

    /**
     * TODO: add docs
     */
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
            throw new \Exception("No field '" . $fieldName . "' in global fields list.");
        }
    }

    /**
     * Casts value if the existing field to specified type.
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
            throw new \Exception("No field '" . $fieldName . "' in global fields list.");
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
     * Returns value of the complex array element by it's complex key.
     *
     * @param array $array Complex associated array (dictionary);
     * @param mixed $complexKey List with hierarchy complex key or a value of the
     *            simple (one level) key.
     *
     * @return mixed Value of the array element if found.
     * @throws \Exception If can't find element by complex key.
     */
    private static function getArrayElement($array, $complexKey) {
        if (!empty($complexKey)) {
            if (is_array($complexKey)) { // Complex key is provided
                $temp = $array;

                foreach ($complexKey as $key) {
                    if (isset($temp[$key])) {
                        $temp = $temp[$key];
                    } else {
                        throw new \Exception("Invalid complex key");
                    }
                }

                return $temp;
            } else { // Simple key is provided
                if (isset($array[$complexKey])) {
                    return $array[$complexKey];
                } else {
                    throw new \Exception("Invalid simple key");
                }
            }
        }
        throw new \Exception("No array element key provided");
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

        return $complexName;
    }

}