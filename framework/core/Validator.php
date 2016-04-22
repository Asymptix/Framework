<?php

namespace Asymptix\core;

use Asymptix\web\Request;

/**
 * Form fields validation functionality.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2009 - 2016, Dmytro Zarezenko
 *
 * @git https://github.com/Asymptix/Framework
 * @license http://opensource.org/licenses/MIT
 */
class Validator {

    /**
     * Validate field value by regular expression.
     *
     * @param string $fieldValue Value of the field.
     * @param string $regexp Regular expression.
     * @return boolean
     */
    public static function validateRegexp($fieldValue, $regexp) {
        return preg_match($regexp, $fieldValue);
    }

    /**
     * Validate if field value is not empty.
     *
     * @param string $fieldName Name of the field.
     * @return boolean
     */
    public static function validateNotEmpty($fieldName) {
        $fieldValue = Request::getFieldValue($fieldName);
        if (is_string($fieldValue)) {
            $fieldValue = trim($fieldValue);
        }
        if (empty($fieldValue)) {
            Errors::saveErrorFor($fieldName, \__ERRORS::FIELD_CANT_BE_EMPTY);
            return false;
        }
        return true;
    }

    /**
     * Validate if checkbox is checked.
     *
     * @param string $fieldName Name of the field.
     * @return boolean
     */
    public static function validateChecked($fieldName) {
        $fieldValue = trim(Request::getFieldValue($fieldName));
        if (empty($fieldValue)) {
            Errors::saveErrorFor($fieldName, \__ERRORS::CHECK_THIS_FIELD);
            return false;
        }
        return true;
    }

    /**
     * Validate if field value is empty.
     *
     * @param string $fieldName Name of the field.
     * @return boolean
     */
    public static function validateEmpty($fieldName) {
        $fieldValue = trim(Request::getFieldValue($fieldName));
        if (empty($fieldValue)) {
            return true;
        }
        return false;
    }

    /**
     * Validate if field value haven't spaces.
     *
     * @param string $fieldName Name of the field.
     *
     * @return boolean
     */
    public static function validateNoSpaces($fieldName) {
        $fieldValue = Request::getFieldValue($fieldName);
        if (strpos($fieldValue, " ") !== false) {
            Errors::saveErrorFor($fieldName, \__ERRORS::SPACES_INACCEPTABLE);
            return false;
        }
        return true;
    }

    /**
     * Validate login value.
     *
     * @param string $fieldName Login field name.
     *
     * @return boolean
     */
    public static function validateLogin($fieldName) {
        if (!validateNotEmpty($fieldName)) {
            return false;
        }
        return validateNoSpaces($fieldName);
    }

    /**
     * Validate if field value is real password.
     *
     * @param string $passwordFieldName Name of password field.
     * @param string $rePasswordFieldName Name of password repeting field.
     *
     * @return boolean
     */
    public static function validatePassword($passwordFieldName, $rePasswordFieldName) {
        $password = trim(Request::getFieldValue($passwordFieldName));
        $rePassword = trim(Request::getFieldValue($rePasswordFieldName));
        if (empty($password)) {
            Errors::saveErrorFor($passwordFieldName, \__ERRORS::EMPTY_PASSWORD);
            return false;
        }

        if ($password != $rePassword) {
            Errors::saveErrorFor($rePasswordFieldName, \__ERRORS::PASSWORDS_ARE_DIFFERENT);
            return false;
        }

        return true;
    }

    /**
     * Validate if captcha code is correct.
     *
     * @param string $fieldName Name of the captcha field.
     *
     * @return boolean
     */
    public static function validateCaptcha($fieldName) {
        if (!validateNotEmpty($fieldName)) {
            return false;
        }
        if (!validateNoSpaces($fieldName)) {
            return false;
        }
        if (Request::getFieldValue($fieldName) != $_SESSION[$fieldName]) {
            Errors::saveErrorFor($fieldName, \__ERRORS::INVALID_CAPTCHA_CODE);
            return false;
        }
        return true;
    }

    /**
     * Validate if field value is real e-mail address.
     *
     * @param string $fieldName Name of the field.
     *
     * @return boolean
     */
    public static function validateEmail($fieldName) {
        $fieldValue = trim(Request::getFieldValue($fieldName));
        if (!self::validateNotEmpty($fieldName)) {
            Errors::saveErrorFor($fieldName, \__ERRORS::INVALID_EMAIL);
            return false;
        } elseif (!self::validateRegexp($fieldValue, "#^[A-Za-z0-9\._-]+@([A-Za-z0-9-]+\.)+[A-Za-z0-9-]+$#")) {
            Errors::saveErrorFor($fieldName, \__ERRORS::INVALID_EMAIL);
            return false;
        }
        return true;
    }

    /**
     * Validate if variable value is correct e-mail address.
     *
     * @param string $value Value of the variable.
     *
     * @return boolean
     */
    public static function isEmail($value) {
        if (empty($value)) {
            return false;
        }
        return self::validateRegexp($value, "#^[A-Za-z0-9\._-]+@([A-Za-z0-9-]+\.)+[A-Za-z0-9-]+$#");
    }

    /**
     * Normalize URL with protocol prefix.
     *
     * @param string $url Unnormalized URL.
     * @param string $protocol Protocol type (default: http).
     *
     * @return string Normalized URL.
     */
    public static function normalizeUrl($url, $protocol = "http") {
        $url = trim($url);
        if (empty($url)) {
            return "";
        }

        if (!self::validateRegexp($url, "#^" . $protocol . "://.+#")) {
            $url = $protocol . "://" . $url;
        }
        $url = preg_replace("#/{3,}#", "//", $url);

        return $url;
    }

    /**
     * Validate if field value is correct URL.
     *
     * @param string $fieldName Name of the field.
     *
     * @return boolean
     */
    public static function validateUrl($fieldName) {
        $fieldValue = Request::getFieldValue($fieldName);

        if (!self::validateNotEmpty($fieldName)) {
            Errors::saveErrorFor($fieldName, \__ERRORS::URL_EMPTY);
            return false;
        } elseif (!self::validateRegexp($fieldValue, "<^(?#Protocol)(?:(?:ht|f)tp(?:s?)\:\/\/|~/|/)?(?#Username:Password)(?:\w+:\w+@)?(?#Subdomains)(?:(?:[-\w]+\.)+(?#TopLevel Domains)(?:com|org|net|gov|mil|biz|info|mobi|name|aero|jobs|museum|travel|[a-z]{2}))(?#Port)(?::[\d]{1,5})?(?#Directories)(?:(?:(?:/(?:[-\w~!$+|.,=]|%[a-f\d]{2})+)+|/)+|\?|#)?(?#Query)(?:(?:\?(?:[-\w~!$+|.,*:]|%[a-f\d{2}])+=(?:[-\w~!$+|.,*:=]|%[a-f\d]{2})*)(?:&(?:[-\w~!$+|.,*:]|%[a-f\d{2}])+=(?:[-\w~!$+|.,*:=]|%[a-f\d]{2})*)*)*(?#Anchor)(?:#(?:[-\w~!$+|.,*:=]|%[a-f\d]{2})*)?$>")) {
            Errors::saveErrorFor($fieldName, \__ERRORS::URL_INVALID);
            return false;
        }

        return true;
    }

    /**
     * Validate if variable value is correct URL.
     *
     * @param string $value Value of the variable.
     *
     * @return boolean
     */
    public static function isUrl($value) {
        return self::validateRegexp(
            $value,
            "<^(?#Protocol)(?:(?:ht|f)tp(?:s?)\:\/\/|~/|/)?(?#Username:Password)(?:\w+:\w+@)?(?#Subdomains)(?:(?:[-\w]+\.)+(?#TopLevel Domains)(?:com|org|net|gov|mil|biz|info|mobi|name|aero|jobs|museum|travel|[a-z]{2}))(?#Port)(?::[\d]{1,5})?(?#Directories)(?:(?:(?:/(?:[-\w~!$+|.,=]|%[a-f\d]{2})+)+|/)+|\?|#)?(?#Query)(?:(?:\?(?:[-\w~!$+|.,*:]|%[a-f\d{2}])+=(?:[-\w~!$+|.,*:=]|%[a-f\d]{2})*)(?:&(?:[-\w~!$+|.,*:]|%[a-f\d{2}])+=(?:[-\w~!$+|.,*:=]|%[a-f\d]{2})*)*)*(?#Anchor)(?:#(?:[-\w~!$+|.,*:=]|%[a-f\d]{2})*)?$>"
        );
    }

    /**
     * Validate maximum length of the text without HTML tags.
     *
     * @global array<string> $_ERRORS Global list of form fields validation errors.
     * @param string $fieldName Name of the field.
     * @param string $maxTextLength Text length limit.
     *
     * @return boolean
     */
    public static function validateTextMaxLength($fieldName, $maxTextLength) {
        global $_ERRORS;

        $text = trim(strip_tags(Request::getFieldValue($fieldName)));
        if (strlen($text) > $maxTextLength) {
            $_ERRORS[$fieldName] = str_replace("[[1]]", $maxTextLength, \__ERRORS::MAX_TEXT_LENGTH);
            return false;
        }
        return true;
    }

    /**
     * Validate minimum length of the text without HTML tags.
     *
     * @global array<string> $_ERRORS Global list of form fields validation errors.
     * @param string $fieldName Name of the field.
     * @param string $minTextLength Text length limit.
     *
     * @return boolean
     */
    public static function validateTextMinLength($fieldName, $minTextLength) {
        global $_ERRORS;

        $text = trim(strip_tags(Request::getFieldValue($fieldName)));
        if (strlen($text) < $minTextLength) {
            $_ERRORS[$fieldName] = str_replace("[[1]]", $minTextLength, \__ERRORS::MIN_TEXT_LENGTH);
            return false;
        }
        return true;
    }

    /**
     * Validate if field value is integer.
     *
     * @param string $fieldName Name of the field.
     * @return boolean
     */
    public static function validateInteger($fieldName) {
        $fieldValue = trim(Request::getFieldValue($fieldName));
        if (!self::validateNotEmpty($fieldName)) {
            Errors::saveErrorFor($fieldName, \__ERRORS::FIELD_CANT_BE_EMPTY);
            return false;
        } elseif (!(/* isDouble($fieldValue) || */ Tools::isInteger($fieldValue))) {
            Errors::saveErrorFor($fieldName, \__ERRORS::INVALID_INTEGER);
            return false;
        }
        return true;
    }

    /**
     * Validate if field value is double.
     *
     * @param string $fieldName Name of the field.
     * @return boolean
     */
    public static function validateDouble($fieldName) {
        $fieldValue = trim(Request::getFieldValue($fieldName));
        $doubleValue = (double) $fieldValue;
        if (!validateNotEmpty($fieldName)) {
            Errors::saveErrorFor($fieldName, \__ERRORS::FIELD_CANT_BE_EMPTY);
            return false;
        } elseif (sprintf("%.2f", $doubleValue) != $fieldValue) {
            Errors::saveErrorFor($fieldName, \__ERRORS::INVALID_NUMBER);
            return false;
        }
        changeFieldValue($fieldName, $doubleValue);
        return true;
    }

    /**
     * Validate if field value is positive numeric value.
     *
     * @param string $fieldName Name of the field.
     * @return boolean
     */
    public static function validatePositive($fieldName) {
        $fieldValue = trim(Request::getFieldValue($fieldName));
        if ($fieldValue <= 0) {
            Errors::saveErrorFor($fieldName, \__ERRORS::INVALID_POSITIVE);
            return false;
        }
        return true;
    }

    /**
     * Validate if login data is correct.
     *
     * @param string $loginFieldName Name of the login field.
     * @param string $passwordFieldName Name of the password field.
     * @return boolean
     */
    public static function validateSignIn($loginFieldName, $passwordFieldName) {
        $login = Request::getFieldValue($loginFieldName);
        $password = Request::getFieldValue($passwordFieldName);

        $validationResult = true;
        if ($login == null || trim($login) == "") {
            Errors::saveErrorFor($loginFieldName, \__ERRORS::EMPTY_LOGIN);
            $validationResult = false;
        }
        if ($password == null || trim($password) == "") {
            Errors::saveErrorFor($passwordFieldName, \__ERRORS::EMPTY_PASSWORD);
            $validationResult = false;
        }

        return $validationResult;
    }

    /**
     * Validate if file was uploaded.
     *
     * @global array $_FILES Global array of uploaded files.
     * @param string $fileFieldName Name of the file field.
     *
     * @return boolean
     */
    public static function validateFileUpload($fileFieldName) {
        global $_FILES;

        if ($_FILES[$fileFieldName]['error'] == UPLOAD_ERR_OK) {
            return true;
        }
        Errors::saveErrorFor($fileFieldName, \__ERRORS::FILE_UPLOAD_ERROR);
        return false;
    }

    /**
     * TODO: add docs
     *
     * @param type $fieldName
     * @param type $range
     * @return boolean
     */
    public static function validateRange($fieldName, $range) {
        $fieldValue = Request::getFieldValue($fieldName);
        if (!in_array($fieldValue, $range)) {
            Errors::saveErrorFor($fieldName, \__ERRORS::INCORRECT_VALUE);
            return false;
        }
        return true;
    }

}
