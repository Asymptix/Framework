<?php

use conf\Config;
use Asymptix\core\Validator;

/**
 * Email class example.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2015, Dmytro Zarezenko
 *
 * @git https://github.com/Asymptix/Framework
 * @license http://opensource.org/licenses/MIT
 */
class Email extends \Asymptix\mail\Email {

    /**
     * Send e-mail notification about user signup to the admin.
     *
     * @global Language $_LANG Global language object.
     * @param User $user Registered User object.
     *
     * @return boolean Returns TRUE if the mail was successfully accepted for
     *           delivery, FALSE otherwise.
     */
    public function sendSignUpAdminNotification($user) {
        global $_LANG;

        return $this->sendNotification(
            Config::EMAIL_ADMIN,
            "New user signup: '" . $user->username . "'",
            $_LANG->code,
            "signup_admin_email",
            array(
                'user_id' => $user->id,
                'username' => $user->username
            ),
            Email::FORMAT_HTML
        );
    }

    /**
     * Send e-mail notification about user signup.
     *
     * @global Language $_LANG Global language object.
     * @param User $user Registered User object.
     *
     * @return boolean Returns TRUE if the mail was successfully accepted for
     *           delivery, FALSE otherwise.
     * @throws \Exception If users e-mail is invalid.
     */
    public function sendSignUpNotification($user) {
        global $_LANG;

        if (!Validator::isEmail($user->email)) {
            throw new \Exception("Invalid email: '" . $user->email . "'");
        }

        return $this->sendNotification(
            $user->email,
            "You registered successfully",
            $_LANG->code,
            "signup_email",
            array(
                'user_id' => $user->id,
                'username' => $user->username
            ),
            Email::FORMAT_HTML
        );
    }

}

?>