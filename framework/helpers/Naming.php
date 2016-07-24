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
class Naming {

    /**
     * Parse usual HTML notation complex field name into array.
     *
     * @param string  $fieldName Field name.
     *
     * @return mixed String or array.
     */
    public static function parseComplexName($fieldName) {
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
    public static function getValueByComplexName($array, $complexKey) {
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

}
