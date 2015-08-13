<?php

namespace Asymptix\localization;

/**
 * Languages functionality class.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2009 - 2015, Dmytro Zarezenko
 * @license http://opensource.org/licenses/MIT
 */
class Languages {

    /**
     * Stores all needed languages and titles in different languages.
     *
     * @var array
     */
    public static $langs = null;

    public static function getLanguages() {
        if (is_null(self::$langs)) {
            self::$langs = array(
                'en' => new Language('en', array(
                    'en' => "English",
                    'de' => "Englisch",
                    'ru' => "Английский",
                    'uk' => "Англійська"
                ), 'gb'),
                'de' => new Language('de', array(
                    'en' => "German",
                    'de' => "Deutsch",
                    'ru' => "Немецкий",
                    'uk' => "Німецька"
                ), 'de'),
                'ru' => new Language('ru', array(
                    'en' => "Russian",
                    'de' => "Rusisch",
                    'ru' => "Русский",
                    'uk' => "Російська"
                ), 'ru'),
                'uk' => new Language('uk', array(
                    'en' => "Ukrainian",
                    'de' => "Ukrainisch",
                    'ru' => "Украинский",
                    'uk' => "Українська"
                ), 'ua'),
            );
        }
        return self::$langs;
    }

    /**
     * Returns Language object by it's ISO code.
     *
     * @param string $code ISO language code.
     * @return Language
     */
    public static function getLanguage($code) {
        $langs = self::getLanguages();
        if (isset($langs[$code])) {
            return $langs[$code];
        } else {
            return $langs['en'];
        }
    }

}

?>