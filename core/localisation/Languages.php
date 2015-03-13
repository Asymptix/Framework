<?php

require_once("core/localisation/Language.php");

/**
 * Works with language in session and POST requests (to set language).
 */
if (isset($_POST['lang'])) {
    $_SESSION['lang'] = $_POST['lang'];
} elseif (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'en';
}
$lang = new Languages();
$_LANG = Languages::getLanguage($_SESSION['lang']);
$_SESSION['lang'] = $_LANG->code;
unset($lang);

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
    public static $langs = array();

    public function Languages() {
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

    /**
     * Returns Language object by it's ISO code.
     *
     * @param string $code ISO language code.
     * @return Language
     */
    public static function getLanguage($code) {
        if (isset(self::$langs[$code])) {
            return self::$langs[$code];
        } else {
            return self::$langs['en'];
        }
    }

}

?>