<?php

namespace Asymptix\web;

/**
 * Session functionality.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2015, Dmytro Zarezenko
 *
 * @git https://github.com/Asymptix/Framework
 * @license http://opensource.org/licenses/MIT
 */
class Session {

    /**
     * Start new or resume existing session.
     *
     * @return mixed This function returns TRUE if a session was successfully
     *          started, otherwise FALSE. If session was resumed returns session
     *          status: PHP_SESSION_DISABLED if sessions are disabled.
     *                  PHP_SESSION_NONE if sessions are enabled, but none exists.
     *                  PHP_SESSION_ACTIVE if sessions are enabled, and one exists.
     */
    public static function start() {
        $status = session_status();
        if ($status == PHP_SESSION_NONE) {
            if (ini_get('session.use_cookies') && isset($_COOKIE['PHPSESSID'])) {
                $sessionId = $_COOKIE['PHPSESSID'];
            } elseif (!ini_get('session.use_only_cookies') && isset($_GET['PHPSESSID'])) {
                $sessionId = $_GET['PHPSESSID'];
            } else {
                return session_start();
            }

            if (!preg_match('/^[a-zA-Z0-9,-]{22,40}$/', $sessionId)) {
                return false;
            }
            return session_start();
        }
        return $status;
    }

    /**
     * Start new or resume existing session.
     *
     * @return mixed This function returns TRUE if a session was successfully
     *          started, otherwise FALSE. If session was resumed returns session
     *          status: PHP_SESSION_DISABLED if sessions are disabled.
     *                  PHP_SESSION_NONE if sessions are enabled, but none exists.
     *                  PHP_SESSION_ACTIVE if sessions are enabled, and one exists.
     */
    public static function open() {
        return self::start();
    }

    /**
     * Sets session variable value.
     *
     * @param string $fieldName
     * @param mixed $fieldValue
     */
    public static function set($fieldName, $fieldValue = true) {
        $_SESSION[$fieldName] = $fieldValue;
    }

    /**
     * Save data to the session.
     *
     * @param array $data
     */
    public static function save(array $data = array()) {
        foreach ($data as $key => $value) {
            $_SESSION[$key] = $value;
        }
    }

    /**
     * Verify if session variable exists.
     *
     * @param string $fieldName
     * @return boolean
     */
    public static function exists($fieldName) {
        return isset($_SESSION[$fieldName]);
    }

    /**
     * Returns value of the session variable exists or null otherwise.
     *
     * @param string $fieldName Variable name.
     * @return mixed Value of the session variable or null if varible with this
     *           name is not exists in teh session.
     */
    public static function get($fieldName) {
        if (self::exists($fieldName)) {
            return $_SESSION[$fieldName];
        }
        return null;
    }

    /**
     * Removes varibale from session.
     *
     * @param string $fieldName Variable name.
     * @return boolean True if variable removed or false if it doesn't exist.
     */
    public static function remove($fieldName) {
        if (self::exists($fieldName)) {
            unset($_SESSION[$fieldName]);

            return true;
        }
        return false;
    }

    /**
     * Destroys all data registered to a session.
     *
     * @return boolean True on success or false on failure.
     */
    public static function destroy() {
        session_unset();
        return session_destroy();
    }

    /**
     * Destroys all data registered to a session.
     *
     * @return boolean True on success or false on failure.
     */
    public static function close() {
        return self::destroy();
    }

}