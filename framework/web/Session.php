<?php

namespace Asymptix\web;

/**
 * Session functionality.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2015 - 2016, Dmytro Zarezenko
 *
 * @git https://github.com/Asymptix/Framework
 * @license http://opensource.org/licenses/MIT
 */
class Session {

    /**
     * Start new or resume existing session.
     *
     * @param string $name [optional] The session name references the name of the
     *           session, which is used in cookies and URLs (e.g. PHPSESSID).
     *           It should contain only alphanumeric characters; it should be short
     *           and descriptive (i.e. for users with enabled cookie warnings).
     *           If name is specified, the name of the current session is changed
     *           to its value.
     *
     *           The session name can't consist of digits only, at least one letter
     *           must be present. Otherwise a new session id is generated every time.
     * @param array $iniSettings [optional] List of parameters for the ini_set() function.
     *
     * @param bool $useCookie [optional] Use cookie or not. All next arguments
     *           works only if this parameter is TRUE.
     * @param int $lifetime [optional] Lifetime of the session cookie, defined in seconds.
     * @param string $path [optional] Path on the domain where the cookie will work.
     *           Use a single slash ('/') for all paths on the domain.
     * @param string $domain [optional] Cookie domain, for example 'www.asymptix.com'.
     *           To make cookies visible on all subdomains then the domain must
     *           be prefixed with a dot like '.asymptix.com'.
     * @param bool $secure [optional] If TRUE cookie will only be sent over secure connections.
     * @param bool $httponly [optional] If set to TRUE then PHP will attempt to
     *           send the httponly flag when setting the session cookie.
     *
     * @return mixed This function returns TRUE if a session was successfully
     *          started, otherwise FALSE. If session was resumed returns session
     *          status: PHP_SESSION_DISABLED if sessions are disabled.
     *                  PHP_SESSION_NONE if sessions are enabled, but none exists.
     *                  PHP_SESSION_ACTIVE if sessions are enabled, and one exists.
     */
    public static function start($name = "", array $iniSettings = [], $useCookie = false,
            $lifetime = null, $path = null, $domain = null, $secure = null, $httponly = null)
    {
        if (!empty($iniSettings)) {
            foreach ($iniSettings as $key => $value) {
                ini_set($key, $value);
            }
        }

        if ($useCookie) {
            session_set_cookie_params($lifetime, $path, $domain, $secure, $httponly);
        }
        if (!empty($name)) {
            session_name($name);
        }

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
