<?php

namespace Asymptix\DB\Beans\Access;

use Asymptix\Core\Tools;

/**
 * Simple User bean class.
 * (You can modify this class according to your database structure)
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2009 - 2015, Dmytro Zarezenko
 * @license http://opensource.org/licenses/MIT
 */
class User extends \Asymptix\DB\DBTimedObject {
    const STATUS_ACTIVATED = 1;
    const STATUS_DEACTIVATED = 0;

    const LOGIN_NOT_ACTIVATED = 0;
    const LOGIN_INVALID_USERNAME = -1;
    const LOGIN_INVALID_PASSWORD = -2;

    const GENDER_NONE = 'none';
    const GENDER_MALE = 'male';
    const GENDER_FEMALE = 'female';

    const TABLE_NAME = "users";
    const ID_FIELD_NAME = "user_id";
    protected $fieldsList = array(
        'user_id' => 0, // int(10) unsigned NOT NULL AUTO_INCREMENT
        'username' => "", // varchar(255) NOT NULL
        'email' => "", // varchar(255) NOT NULL
        'password' => "", // varchar(255) NOT NULL
        'auth_key' => "", // varchar(32) NOT NULL
        'role' => 0, // int(1) unsigned not null

        'full_name' => "", // VARCHAR(255) NOT NULL
        'gender' => self::GENDER_NONE, // ENUM( 'male', 'female', 'none', '' ) NOT NULL DEFAULT 'none'
        'language' => "en", // VARCHAR(2) NOT NULL DEFAULT 'en'

        'last_login_time' => "0000-00-00 00:00:00", // datetime DEFAULT NULL
        'create_time' => "0000-00-00 00:00:00", // datetime DEFAULT NULL
        'create_user_id' => 0, // int(11) DEFAULT NULL
        'update_time' => "0000-00-00 00:00:00", // datetime DEFAULT NULL
        'update_user_id' => 0, // int(11) DEFAULT NULL

        'activation' => 0, // tinyint(1) NOT NULL DEFAULT '0'

        'signature' => "", // TEXT NOT NULL
        'avatar' => "", // varchar(100) not null

        // additional fields according to your database structure
    );

    public function User() {
        // initialisation
    }

    /**
     * For user accounts we must verify if login field ID is unique.
     */
    public function save() {
        try {
            return parent::save();
        } catch (DBException $ex) {
            print($ex->getMessage());exit();
            return false; //TODO: maybe verify if duplicate or other error
        }
    }

    /**
     * Password encoding method.
     *
     * @param string $password Password
     * @return string Encoded password string.
     */
    public static function passwordEncode($password) {
        return md5($password);
    }

    /**
     * Login functionality.
     *
     * @param string $login Username from login form.
     * @param string $password Password from login form.
     *
     * @return mixed User object on success or integer result code if some problems occurred.
     */
    public static function login($login, $password) {
        $selector = new DBSelector(new User());
        $user = $selector->selectDBObjectByField('email', $login);
        if (Tools::isInstanceOf($user, new User())) {
            if ($user->isActivated()) {
                if ($user->password == self::passwordEncode($password)) {
                    $user->updateLoginTime();
                    return $user;
                }

                return self::LOGIN_INVALID_PASSWORD;
            }
            return self::LOGIN_NOT_ACTIVATED;
        }
        return self::LOGIN_INVALID_USERNAME;
    }

    /**
     * Updates login time.
     */
    public function updateLoginTime() {
        $query = "UPDATE " . self::TABLE_NAME . "
                     SET last_login_time = NOW()
                   WHERE " . self::ID_FIELD_NAME . " = ?";
        DBCore::doUpdateQuery($query, "i", array($this->id));
    }

    /**
     * Checks if user is logged in.
     *
     * @global User $_USER Current user object.
     *
     * @return boolean
     */
    public static function checkLoggedIn() {
        global $_USER;

        if (Tools::isInstanceOf($_USER, new User())) {
            return true;
        }
        return false;
    }

    /**
     * Checks if account of the current user is equal to the needed account page.
     *
     * @global User $_USER Current user object.
     * @param array $roles Needed roles.
     *
     * @return boolean
     */
    public static function checkAccountAccess($roles = array()) {
        global $_USER;

        if (self::checkLoggedIn()) {
            if (empty($roles)) {
                return true;
            }

            if (!is_array($roles)) {
                $roles = array($roles);
            }
            foreach ($roles as $role) {
                if ($_USER->role == $role) {
                    return true;
                }
            }
            return false;
        }
        return false;
    }

    /**
     * Logout functionality.
     */
    public static function logout() {
        $_USER = null;
        session_unset();
    }

    /**
     * Returns current users avatar image file path.
     *
     * @param boolean $icon Return only default icon flag.
     *
     * @return string Image path.
     */
    public function getAvatarPath($icon = false) {
        $currentAvatarFileName = $this->avatar;
        if (!empty($currentAvatarFileName) && file_exists(Config::DIR_AVATARS . $currentAvatarFileName)) {
            return Config::DIR_AVATARS . $currentAvatarFileName;
        }
        if ($icon) {
            return "img/user_avatar.png";
        }
        return "img/placehold/100x100.png";
    }

    /**
     * Updates users avatar image file path in the DB.
     *
     * @param string $newAvatarFileName New filename.
     *
     * @return boolean Success flag.
     */
    public function updateAvatar($newAvatarFileName) {
        $currentAvatarFileName = $this->avatar;
        if (!empty($currentAvatarFileName) && file_exists(Config::DIR_AVATARS . $currentAvatarFileName)) {
            unlink(Config::DIR_AVATARS . $currentAvatarFileName);
        }

        if (file_exists(Config::DIR_AVATARS . $newAvatarFileName)) {
            $query = "UPDATE " . self::TABLE_NAME
                    . " SET avatar = ?"
                    . " WHERE " . self::ID_FIELD_NAME . " = ?";
            if (DBCore::doUpdateQuery($query, "si", array(
                $newAvatarFileName,
                $this->id
            ))) {
                $this->avatar = $newAvatarFileName;
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

}

?>