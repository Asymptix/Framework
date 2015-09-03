<?php

namespace Asymptix\localization;

use Asymptix\core\Tools;
use Asymptix\core\Errors;

/**
 * Localization class for using multiple languages and store localized lexems
 * into the database.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2010 - 2015, Dmytro Zarezenko
 *
 * @git https://github.com/Asymptix/Framework
 * @license http://opensource.org/licenses/MIT
 */
class Localization {
    /**
     * Return localized value for some value.
     *
     * @global array<mixed> $_CONFIG Global configuration.
     * @param string, array<string> $value Value for localization, string or
     *           associative array of language key and value.
     *
     * @return array<string> Localized value as associative array of string values
     *           for all languages.
     */
    static public function localize($value = "") {
        $localizedValue = Languages::$langs;
        if (is_array($value)) {
            foreach ($localizedValue as $langKey => &$lValue) {
                if (isset($value[$langKey])) {
                    $lValue = $value[$langKey];
                } else {
                    $lValue = "";
                }
            }
        } elseif (is_string($value)) {
            foreach ($localizedValue as &$lValue) {
                $lValue = $value;
            }
        } else {
            throw new Exception("Invalid value for localization");
        }

        return $localizedValue;
    }

    /**
     * Validate if localised field value is not empty.
     *
     * @param string $fieldName Name of the field.
     *
     * @return boolean
     */
    static public function validateNotEmpty($fieldName) {
        $fieldValue = Tools::getFieldValue($fieldName);
        if (is_array($fieldValue)) {
            foreach ($fieldValue as $value) {
                $value = trim($value);
                if (empty($value)) {
                    Errors::saveErrorFor($fieldName, \__ERRORS::EMPTY_FIELD);

                    return false;
                }
            }
        } else {
            return validateNotEmpty($fieldName);
        }
        return true;
    }
}