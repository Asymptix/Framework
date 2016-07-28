<?php

namespace Asymptix\core;

/*
 * Global fields values array.
 */
$_FIELDS = [];

/*
 * Global fields associated errors array.
 */
$_ERRORS = [];

/**
 * Form fields errors functionality.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2009 - 2016, Dmytro Zarezenko
 *
 * @git https://github.com/Asymptix/Framework
 * @license http://opensource.org/licenses/MIT
 */
class Errors {

    /**
     * Display error of script execution.
     *
     * @param string $errorMessage Message of the error.
     */
    public static function displayError($errorMessage, $fieldName = null) {
        if (!is_null($fieldName)) {
            return ('<label for="' . $fieldName . '" class="form-error">' . $errorMessage . '</label>');
        } else {
            return ('<span class="label label-danger pull-right form-error">' . $errorMessage . '</span>');
        }
    }

    /**
     * Display error for field if it's exist.
     *
     * @global array $_ERRORS List of fields errors.
     * @param string $fieldName Name of the field.
     */
    public static function displayErrorFor($fieldName) {
        global $_ERRORS;

        if (self::isSetErrorFor($fieldName)) {
            return self::displayError($_ERRORS[$fieldName], $fieldName);
        }

        return "";
    }

    /**
     * Returns error message by field name if exists.
     *
     * @global array $_ERRORS Global list of fields errors.
     * @param string $fieldName Name of the field.
     *
     * @return string Error message.
     */
    public static function getError($fieldName) {
        global $_ERRORS;

        if (self::isSetErrorFor($fieldName)) {
            return $_ERRORS[$fieldName];
        }

        return "";
    }

    /**
     * Test if error for field is exist.
     *
     * @global array $_ERRORS Global list of fields errors.
     * @param string $fieldName Name of the field.
     *
     * @return bool
     */
    public static function isSetErrorFor($fieldName) {
        global $_ERRORS;

        return isset($_ERRORS[$fieldName]);
    }

    /**
     * Checks if some common errors exists.
     *
     * @global array $_ERRORS
     *
     * @return bool
     */
    public static function isErrorsExist() {
        global $_ERRORS;

        return isset($_ERRORS['_common']) && !empty($_ERRORS['_common']);
    }

    /**
     * Returns common errors array.
     *
     * @global array $_ERRORS
     *
     * @return array
     */
    public static function getErrors() {
        global $_ERRORS;

        return isset($_ERRORS['_common']) ? $_ERRORS['_common'] : [];
    }

    /**
     * Save error message text for a field in global errors list.
     *
     * @global array $_ERRORS Global list of fields errors.
     * @param string $fieldName Name of the field.
     * @param string $errorMessageText Text of the error message.
     */
    public static function saveErrorFor($fieldName, $errorMessageText) {
        global $_ERRORS;

        $_ERRORS[$fieldName] = $errorMessageText;
    }

    /**
     * Save error message text for a field in global errors list.
     *
     * @global array $_ERRORS Global list of fields errors.
     * @param string $errorMessageText Text of the error message.
     */
    public static function saveError($errorMessageText) {
        global $_ERRORS;

        if (!isset($_ERRORS['_common'])) {
            $_ERRORS['_common'] = [];
        }
        $_ERRORS['_common'][] = $errorMessageText;
    }

}
