<?php

/**
 * Stores Language code and titles in different languages.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2010 - 2015, Dmytro Zarezenko
 * @license http://opensource.org/licenses/MIT
 */
class Language {
    /**
     * ISO 639-1 standard two-letter language code.
     *
     * @var string
     */
    public $code;

    /**
     * Two-letter flag code of the language.
     *
     * @var string
     */
    public $flag;

    /**
     * List of language titles on different languages.
     *
     * @var array<string, string>
     */
    public $titles = array();

    public function Language($code, $titles, $flag = "") {
        $this->code = $code;
        $this->titles = $titles;
        $this->flag = $flag;
    }

    /**
     * Returns title of the language by it's ISO code.
     * If code is not set then returns native title of the language.
     *
     * @param string $code 2-letters language ISO code (optional).
     *
     * @return string Title of the language.
     * @throws Exception If language code is incorrect.
     */
    public function getTitle($code = null) {
        if (empty($code)) {
            $code = $this->code;
        }

        if (isset($this->titles[$code])) {
            return $this->titles[$code];
        } else {
            throw new Exception("Invalid language code '" . $code . "'");
        }
    }

}

?>