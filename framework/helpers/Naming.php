<?php

namespace Asymptix\helpers;

/**
 * All naming functionality.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2016, Dmytro Zarezenko
 *
 * @git https://github.com/Asymptix/Framework
 * @license http://opensource.org/licenses/MIT
 */
class Naming
{

    /**
     * Parse usual HTML notation complex field name into array.
     *
     * @param string  $fieldName Field name.
     *
     * @return mixed String or array.
     */
    public static function parseComplexName($fieldName) {
        $normName = str_replace(
            ['][', '[', ']'],
            ['|', '|', ''],
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

    /**
     * Returns value of the complex array element by it's complex key.
     *
     * @param array $array Complex associated array (dictionary);
     * @param mixed $complexName List with hierarchy complex key or a string
     *            value of complex or simple (one level) key.
     *
     * @return mixed Value of the array element if found.
     * @throws \Exception If can't find element by complex key or no name provided.
     */
    public static function getValueByComplexName($array, $complexName) {
        if (!is_array($complexName)) {
            $complexName = self::parseComplexName($complexName);
        }

        if (!empty($complexName)) {
            if (is_array($complexName)) { // Complex key is provided
                $temp = $array;

                foreach ($complexName as $key) {
                    if (isset($temp[$key])) {
                        $temp = $temp[$key];
                    } else {
                        throw new \Exception("Invalid complex key");
                    }
                }

                return $temp;
            } else { // Simple key is provided
                if (isset($array[$complexName])) {
                    return $array[$complexName];
                } else {
                    throw new \Exception("Invalid simple key");
                }
            }
        }
        throw new \Exception("No name provided");
    }

    /**
     * Sets value of the complex array element by it's complex key.
     *
     * @param array $array Complex associated array (dictionary);
     * @param mixed $complexName List with hierarchy complex key or a string
     *            value of complex or simple (one level) key.
     * @param mixed $value Value.
     * @param bool $rewrite Rewrite existed values with the same name tree or not.
     *
     * @throws \Exception If can't find element by complex key or no name provided.
     */
    public static function setValueWithComplexName(&$array, $complexName, $value, $rewrite = false) {
        if (!is_array($complexName)) {
            $complexName = self::parseComplexName($complexName);
        }

        if (!empty($complexName)) {
            if (is_array($complexName)) { // Complex key is provided
                for ($i = 0; $i < count($complexName); $i++) {
                    $key = $complexName[$i];

                    if ($i < (count($complexName) - 1)) {
                        if (!isset($array[$key])) { // declare value as empty array as not last element
                            $array[$key] = [];
                        } else {
                            if (!is_array($array[$key])) { // detect if current value is array because not last element
                                if ($rewrite) {
                                    $array[$key] = [];
                                } else {
                                    throw new \Exception(
                                        "Try to assign value as array element to the not an array"
                                    );
                                }
                            }
                        }
                        $array = &$array[$key];
                    } else { // last element
                        $array[$key] = $value;
                    }
                }
            } else { // Simple key is provided
                $array[$complexName] = $value;
            }

            return;
        }
        throw new \Exception("No name provided");
    }

    /**
     * Removes value of the complex array element by it's complex key.
     *
     * @param array $array Complex associated array (dictionary);
     * @param mixed $complexName List with hierarchy complex key or a string
     *            value of complex or simple (one level) key.
     *
     * @throws \Exception If no name provided.
     */
    public static function unsetValueWithComplexName(&$array, $complexName) {
        if (!is_array($complexName)) {
            $complexName = self::parseComplexName($complexName);
        }

        if (!empty($complexName)) {
            if (is_array($complexName)) { // Complex key is provided
                for ($i = 0; $i < count($complexName); $i++) {
                    $key = $complexName[$i];

                    if ($i < (count($complexName) - 1)) {
                        if (!isset($array[$key]) || !is_array($array[$key])) {
                            break;
                        }
                        $array = &$array[$key];
                    } elseif(isset($array[$key])) { // last element
                        unset($array[$key]);
                    }
                }
            } elseif (isset($array[$complexName])) { // Simple key is provided
                unset($array[$complexName]);
            }

            return;
        }
        throw new \Exception("No name provided");
    }

    /**
     * Removes value of the complex array element by it's complex key.
     *
     * @param array $array Complex associated array (dictionary);
     * @param mixed $complexName List with hierarchy complex key or a string
     *            value of complex or simple (one level) key.
     *
     * @throws \Exception If no name provided.
     */
    public static function deleteValueWithComplexName(&$array, $complexName) {
        self::unsetValueWithComplexName($array, $complexName);
    }

    /**
     * Removes value of the complex array element by it's complex key.
     *
     * @param array $array Complex associated array (dictionary);
     * @param mixed $complexName List with hierarchy complex key or a string
     *            value of complex or simple (one level) key.
     *
     * @throws \Exception If no name provided.
     */
    public static function removeValueWithComplexName(&$array, $complexName) {
        self::unsetValueWithComplexName($array, $complexName);
    }

}
