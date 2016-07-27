<?php

namespace Asymptix\core;

/**
 * Content manipulations functionality.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2015 - 2016, Dmytro Zarezenko
 *
 * @git https://github.com/Asymptix/Framework
 * @license http://opensource.org/licenses/MIT
 */
class Content {

    /**
     * Renders some template to the string value.
     * You could use $_TPL list inside the template to pass variables to the
     * template.
     *
     * @param string $tplPath Path to the template.
     * @param array $tplVariables Variables list.
     *
     * @return string
     */
    public static function render($tplPath, $tplVariables = []) {
        $_TPL = $tplVariables;

        ob_start();
        include($tplPath);
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }

    /**
     * Performs str_replace() on a content.
     *
     * @param string $content Content.
     * @param array $tplVariables Replacements list, keys will be replaced by
     *           values.
     *
     * @return string
     */
    public static function replace($content, $tplVariables = []) {
        return str_replace(
            array_keys($tplVariables),
            array_values($tplVariables),
            $content
        );
    }

    /**
     * Returns XSS secured string before output it (use htmlspecialchars()).
     *
     * @param string $value Value to secure.
     * @param int $flags A bitmask of one or more of the following flags,
     *           which specify how to handle quotes, invalid code unit sequences
     *           and the used document type. The default is ENT_QUOTES.
     * @param string $encoding An optional argument defining the encoding used
     *           when converting characters. The default is 'UTF-8'.
     *
     * @return string  The converted string. If the input string contains an
     *           invalid code unit sequence within the given encoding an empty
     *           string will be returned, unless either the ENT_IGNORE or
     *           ENT_SUBSTITUTE flags are set.
     */
    public static function secure($value, $flags = ENT_QUOTES, $encoding = 'UTF-8') {
        return htmlspecialchars($value, $flags, $encoding, false);
    }

}
