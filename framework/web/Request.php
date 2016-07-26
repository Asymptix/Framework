<?php

namespace Asymptix\web;

use Asymptix\core\Tools;
use Asymptix\helpers\Naming;

/**
 * Request functionality.
 *
 * @category Asymptix PHP Framework
 *
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2009 - 2016, Dmytro Zarezenko
 *
 * @git https://github.com/Asymptix/Framework
 *
 * @license http://opensource.org/licenses/MIT
 */
class Request
{
    /**
     * Check if form was submitted.
     *
     * @param string $submitFieldName Name of the submit field (button).
     *                                Default: "submitBtn"
     *
     * @return bool
     */
    public static function isFormSubmitted($submitFieldName = 'submitBtn')
    {
        return self::getFieldValue($submitFieldName) !== null;
    }

    /**
     * Verify if field is exists in the request.
     *
     * @param mixed  $fieldName String name of the field or complex name as array.
     * @param string $source    Http::GET or Http::POST constant.
     *
     * @return bool
     */
    public static function issetField($fieldName, $source = null)
    {
        return !is_null(self::getFieldValue($fieldName, $source));
    }

    /**
     * Gets value of the field from $_REQUEST or $_SESSION (is some REQUEST values
     * needs to be stored by scenario). Also it takes values from $_GET or $_POST
     * separately if second parameter is passed.
     *
     * @param mixed  $fieldName String name of the field or complex hierarchy name.
     * @param string $source    Http::GET or Http::POST constant.
     *
     * @return mixed Value of the field, NULL otherwise.
     */
    public static function getFieldValue($fieldName, $source = null)
    {
        $value = null;

        try {
            switch ($source) {
                case Http::GET:
                    $value = Naming::getValueByComplexName($_GET, $fieldName);
                    break;
                case Http::POST:
                    $value = Naming::getValueByComplexName($_POST, $fieldName);
                    break;
                default:
                    $value = Naming::getValueByComplexName($_REQUEST, $fieldName);
            }
        } catch (\Exception $ex) {
            try {
                if (Session::exists('_post')) {
                    $value = Naming::getValueByComplexName(
                        Session::get('_post'), $fieldName
                    );
                }
            } catch (\Exception $ex) {
                return;
            }
        }

        if (!is_null($value)) {
            if (is_array($value)) {
                return $value;
            } elseif (is_int($value)) {
                return intval($value);
            }

            return $value;
        }
    }

    /**
     * Returns value of the HTTP GET requet field.
     *
     * @param mixed $fieldName    String name of the field or complex name as array.
     * @param mixed $defaultValue Default value.
     *
     * @return mixed Value of the field, NULL otherwise.
     */
    public static function _get($fieldName, $defaultValue = null)
    {
        $fieldValue = self::getFieldValue($fieldName, Http::GET);
        if (is_null($fieldValue) && !is_null($defaultValue)) {
            return $defaultValue;
        }

        return $fieldValue;
    }

    /**
     * Returns value of the HTTP POST requet field.
     *
     * @param mixed $fieldName    String name of the field or complex name as array.
     * @param mixed $defaultValue Default value.
     *
     * @return mixed Value of the field, NULL otherwise.
     */
    public static function _post($fieldName, $defaultValue = null)
    {
        $fieldValue = self::getFieldValue($fieldName, Http::POST);
        if (is_null($fieldValue) && !is_null($defaultValue)) {
            return $defaultValue;
        }

        return $fieldValue;
    }

    /**
     * Returns value of the HTTP POST or GET requet field.
     *
     * @param mixed $fieldName    String name of the field or complex name as array.
     * @param mixed $defaultValue Default value.
     *
     * @return mixed Value of the field, NULL otherwise.
     */
    public static function _field($fieldName, $defaultValue = null)
    {
        $fieldValue = self::getFieldValue($fieldName);
        if (is_null($fieldValue) && !is_null($defaultValue)) {
            return $defaultValue;
        }

        return $fieldValue;
    }

    /**
     * Returns value of the filter field.
     *
     * @param string $filterName   Name of the filter field.
     * @param mixed  $defaultValue Default value.
     *
     * @return mixed
     */
    public static function _filter($filterName, $defaultValue)
    {
        return Tools::getFilterValue($filterName, $defaultValue);
    }

    /**
     * Sets value of the field or creates new field by pair $fieldName => $fieldValue.
     *
     * @global array<mixed> $_FIELDS Global fields list.
     *
     * @param mixed $fieldName  Name of the field as a string or complex name as
     *                          an array.
     * @param mixed $fieldValue Value of the field.
     *
     * @throws \Exception
     */
    public static function setFieldValue($fieldName, $fieldValue)
    {
        global $_FIELDS;

        Naming::setValueWithComplexName($_FIELDS, $fieldName, $fieldValue);
    }

    /**
     * Remembers field in session.
     *
     * @param string $fieldName  Name of the field.
     * @param mixed  $fieldValue
     */
    public static function rememberField($fieldName, $fieldValue)
    {
        Session::set("_post[{$fieldName}]", serialize($fieldValue));
    }

    /**
     * Forget cross session field.
     *
     * @param string $fieldName Field name.
     */
    public static function forgetField($fieldName)
    {
        Naming::unsetValueWithComplexName($_SESSION, "_post[{$fieldName}]");
    }

    /**
     * Forget all cross session fields.
     *
     * @return bool
     */
    public static function forgetFields()
    {
        return Session::remove('_post');
    }

    /**
     * Change value of the existing field.
     *
     * @global array<mixed> $_FIELDS Global fields list.
     *
     * @param string $fieldName  Name of the field.
     * @param mixed  $fieldValue Value of the field.
     */
    public static function changeFieldValue($fieldName, $fieldValue)
    {
        global $_FIELDS;

        if (isset($_FIELDS[$fieldName])) {
            $_FIELDS[$fieldName] = $fieldValue;
        } else {
            throw new \Exception("No field '".$fieldName."' in global fields list.");
        }
    }

    /**
     * Casts value if the existing field to specified type.
     *
     * @global array<mixed> $_FIELDS Global fields list.
     *
     * @param string $fieldName Name of the field.
     * @param string $type      New field value type.
     */
    public static function castFieldValue($fieldName, $type)
    {
        global $_FIELDS;

        if (isset($_FIELDS[$fieldName])) {
            switch ($type) {
                case 'integer':
                case 'int':
                case 'i':
                    $_FIELDS[$fieldName] = intval($_FIELDS[$fieldName]);
                    break;
                case 'real':
                case 'float':
                case 'double':
                case 'd':
                    $_FIELDS[$fieldName] = floatval($_FIELDS[$fieldName]);
                    break;
                case 'string':
                case 'str':
                case 's':
                    $_FIELDS[$fieldName] = strval($_FIELDS[$fieldName]);
                    break;
                case 'boolean':
                case 'bool':
                case 'b':
                    $_FIELDS[$fieldName] = (bool) $_FIELDS[$fieldName];
            }
        } else {
            throw new \Exception("No field '".$fieldName."' in global fields list.");
        }
    }

    /**
     * Normilize all boolean checkboxes even they are not checked.
     *
     * @global array $_FIELDS Submitted form fields.
     *
     * @param array<string> $fieldNames Names of all boolean checkboxes what need
     *                                  fixes.
     */
    public static function normalizeCheckboxes($fieldNames)
    {
        global $_FIELDS;

        foreach ($fieldNames as $fieldName) {
            $_FIELDS[$fieldName] = (int) (bool) self::getFieldValue($fieldName);
        }
    }

    /**
     * Removes fields from global fields list.
     *
     * @param array<string> $fieldNames Names of all boolean checkboxes what need
     *                                  fixes (may be list of complex field names).
     */
    public static function removeFields($fieldNames)
    {
        global $_FIELDS;

        foreach ($fieldNames as $fieldName) {
            Naming::unsetValueWithComplexName($_FIELDS, $fieldName);
        }
    }
}
