<?php

namespace Asymptix\core;

/**
 * Messages functionlity.
 *
 * @category Asymptix PHP Framework
 *
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2013 - 2016, Dmytro Zarezenko
 *
 * @git https://github.com/Asymptix/Framework
 *
 * @license http://opensource.org/licenses/MIT
 */
class Messages
{
    const MSG_EMPTY = 0;
    const MSG_INFO = 1;
    const MSG_SUCCESS = 2;
    const MSG_WARNING = 3;
    const MSG_ERROR = 4;

    /**
     * Messages list.
     *
     * @var array
     */
    private static $messages = [];

    /**
     * Push new message to the global messages list.
     *
     * @param int    $type Priority type of the message.
     * @param string $text Text of the message.
     * @param string $code Code of the message in the message list (optional).
     *
     * @throws \Exception If wrong priority message type provided.
     */
    public static function pushMessage($type, $text, $code = null)
    {
        $oClass = new \ReflectionClass(new self());
        $constantsList = $oClass->getConstants();

        if (in_array($type, $constantsList)) {
            if (empty($code)) {
                self::$messages[] = new __MSG($type, $text);
            } else {
                self::$messages[$code] = new __MSG($type, $text);
            }
        } else {
            throw new \Exception('Invalid message type code');
        }
    }

    /**
     * Push new message to the global messages list.
     *
     * @param int    $type Priority type of the message.
     * @param string $text Text of the message.
     * @param string $code Code of the message in the message list (optional).
     *
     * @throws \Exception If wrong priority message type provided.
     */
    public static function addMessage($type, $text, $code = null)
    {
        return self::pushMessage($type, $text, $code);
    }

    /**
     * Returns message by it's code from global messages list.
     *
     * @param string $code Code of the message in the message list.
     *
     * @throws \Exception If message with such code is not exists.
     *
     * @return __MSG Message object.
     */
    public static function getMessage($code)
    {
        if (isset(self::$messages[$code])) {
            return self::$messages[$code];
        } else {
            throw new \Exception('Invalid message code');
        }
    }

    /**
     * Returns message text (content).
     *
     * @param mixed $code Key of the message.
     *
     * @return string
     */
    public static function get($code)
    {
        try {
            $msg = self::getMessage($code);

            return $msg->text;
        } catch (\Exception $ex) {
            return '';
        }
    }

    /**
     * Removes message with some code from global messages list.
     *
     * @param string $code Code of the message in the message list.
     */
    public static function popMessages($code)
    {
        if (isset(self::$messages[$code])) {
            unset(self::$messages[$code]);
        }
    }

    /**
     * Removes message with some code from global messages list.
     *
     * @param string $code Code of the message in the message list.
     */
    public static function removeMessages($code)
    {
        return self::popMessages($code);
    }

    /**
     * Sort messages list with most important messages priority but save order if
     * priority the same.
     */
    public static function reorderMessages()
    {
        uasort(self::$messages, function ($a, $b) {
            return $a->type <= $b->type;
        });
    }

    /**
     * Sort messages list with most important messages priority but save order if
     * priority the same.
     */
    public static function sortMessages()
    {
        return self::reorderMessages();
    }

    /**
     * Returns reordered by priority messages list.
     *
     * @return array<_MSG> List with reordered by prioroty messages.
     */
    public static function getMessages()
    {
        self::reorderMessages();

        return self::$messages;
    }
}

/**
 * Message item helper class.
 */
class __MSG
{
    public $type = null;
    public $text = '';

    public function __construct($type, $text)
    {
        $this->type = $type;
        $this->text = trim($text);
    }
}
