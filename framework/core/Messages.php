<?php

namespace Asymptix\core;

$_MESSAGES = array();

/**
 * Messages functionlity.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2013 - 2015, Dmytro Zarezenko
 *
 * @git https://github.com/dzarezenko/Asymptix-PHP-Framework.git
 * @license http://opensource.org/licenses/MIT
 */
class Messages {

    const MSG_EMPTY = 0;
    const MSG_INFO = 1;
    const MSG_SUCCESS = 2;
    const MSG_WARNING = 3;
    const MSG_ERROR = 4;

    /**
     * Push new message to the global messages list.
     *
     * @global array<_MSG> $_MESSAGES Global list with messages.
     *
     * @param int $type Priority type of the message.
     * @param string $text Text of the message.
     * @param string $code Code of the message in the message list (optional).
     *
     * @throws \Exception If wrong priority message type provided.
     */
    public static function pushMessage($type, $text, $code = null) {
        global $_MESSAGES;

        $oClass = new \ReflectionClass('Messages');
        $constantsList = $oClass->getConstants();

        if (in_array($type, $constantsList)) {
            if (empty($code)) {
                $_MESSAGES[] = new __MSG($type, $text);
            } else {
                $_MESSAGES[$code] = new __MSG($type, $text);
            }
        } else {
            throw new \Exception("Invalid message type code");
        }
    }

    /**
     * Push new message to the global messages list.
     *
     * @global array<_MSG> $_MESSAGES Global list with messages.
     *
     * @param int $type Priority type of the message.
     * @param string $text Text of the message.
     * @param string $code Code of the message in the message list (optional).
     *
     * @throws \Exception If wrong priority message type provided.
     */
    public static function addMessage($type, $text, $code = null) {
        return self::pushMessage($type, $text, $code);
    }

    /**
     * Returns message by it's code from global messages list.
     *
     * @global array<_MSG> $_MESSAGES Global list with messages.
     *
     * @param string $code Code of the message in the message list.
     *
     * @return __MSG Message object.
     *
     * @throws \Exception If message with such code is not exists.
     */
    public static function getMessage($code) {
        global $_MESSAGES;

        if (isset($_MESSAGES[$code])) {
            return $_MESSAGES[$code];
        } else {
            throw new \Exception("Invalid message code");
        }
    }

    /**
     * Removes message with some code from global messages list.
     *
     * @global array<_MSG> $_MESSAGES Global list with messages.
     *
     * @param string $code Code of the message in the message list.
     */
    public static function popMessages($code) {
        global $_MESSAGES;

        if (isset($_MESSAGES[$code])) {
            unset($_MESSAGES[$code]);
        }
    }

    /**
     * Removes message with some code from global messages list.
     *
     * @global array<_MSG> $_MESSAGES Global list with messages.
     *
     * @param string $code Code of the message in the message list.
     */
    public static function removeMessages($code) {
        return self::popMessages($code);
    }

    /**
     * Sort messages list with most important messages priority but save order if
     * priority the same.
     *
     * @global array<_MSG> $_MESSAGES Global list with messages.
     */
    public static function reorderMessages() {
        global $_MESSAGES;

        uasort($_MESSAGES, array('__MSG', "cmp"));
    }

    /**
     * Sort messages list with most important messages priority but save order if
     * priority the same.
     *
     * @global array $_MESSAGES
     */
    public static function sortMessages() {
        return self::reorderMessages();
    }

    /**
     * Returns reordered by priority messages list.
     *
     * @global array<_MSG> $_MESSAGES Global list with messages.
     * @return array<_MSG> List with reordered by prioroty messages.
     */
    public static function getMessages() {
        global $_MESSAGES;

        self::reorderMessages();
        return $_MESSAGES;
    }

}

/**
 * Message item helper class.
 */
class __MSG {

    public $type = null;
    public $text = "";

    public function __MSG($type, $text) {
        $this->type = $type;
        $this->text = trim($text);
    }

    public static function cmp($a, $b) {
        return ($a->type <= $b->type);
    }

}

?>