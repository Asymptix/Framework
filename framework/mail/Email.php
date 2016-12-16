<?php

namespace Asymptix\mail;

use Asymptix\core\Validator;
use Asymptix\core\Content;

/**
 * Mail send functionality wrapper.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2010 - 2016, Dmytro Zarezenko
 *
 * @git https://github.com/Asymptix/Framework
 * @license http://opensource.org/licenses/MIT
 */
class Email
{

    /**
     * Mail format constants.
     */
    const FORMAT_TEXT = "text";
    const FORMAT_HTML = "html";

    /**
     * From e-mail address.
     *
     * @var string
     */
    private $fromEmail;

    /**
     * From name.
     *
     * @var string
     */
    private $fromName;

    /**
     * Absolute path to the e-mail templates folder with ending '/'.
     *
     * @var string
     */
    private $tplFolder;

    /**
     * Filename of the e-mail signature template (must be in e-mail templates folder).
     *
     * @var string
     */
    private $signatureTpl;

    /**
     * Email class constructor. Initialize basic e-mail send functionality
     * variables and validate from e-mail address.
     *
     * @param string $fromEmail From e-mail address.
     * @param string $fromName From name.
     * @param string $tplFolder Absolute path to the e-mail templates folder
     *           with ending '/'.
     * @param string $signatureTpl Filename of the e-mail signature template
     *           (must be in e-mail templates folder).
     *
     * @throws Exception If provided sender e-mail address is invalid.
     */
    public function __construct($fromEmail, $fromName, $tplFolder, $signatureTpl = "") {
        if (!Validator::isEmail($fromEmail)) {
            throw new EmailException("Invalid from e-mail address");
        }

        $this->fromEmail = $fromEmail;
        $this->fromName = $fromName;
        $this->tplFolder = $tplFolder;
        $this->signatureTpl = $signatureTpl;
    }

    /**
     * Sends mail.
     *
     * @param string $email E-mail of the reciver.
     * @param string $subject Subject of the email to be sent.
     * @param string $message Text of the mail.
     * @param string $format E-mail format ("text" or "html").
     * @param string $replyTo Reply to e-mail address.
     *
     * @return bool Returns TRUE if the mail was successfully accepted for
     *           delivery, FALSE otherwise.
     */
    protected function sendMail($email, $subject, $message, $format = self::FORMAT_TEXT, $replyTo = "") {
        $headers = "From: " . $this->fromName . " <" . $this->fromEmail . ">\r\n";
        $headers.= "Reply-To: " . $replyTo . "\r\n";

        if ($format == self::FORMAT_HTML) {
            $headers.= "MIME-Version: 1.0\r\n";
            $headers.= "Content-type: text/html; charset=utf-8\r\n";
        }

        /*
         * Send generated message
         */
        return mail($email, $subject, $message, $headers);
    }

    /**
     * Send mail with standard PHP mail() function.
     * See more details in the standard PHP mail() function documentation.
     *
     * @param string $to Receiver, or receivers of the mail.
     * @param string $subject Subject of the email to be sent.
     * @param mixed $message String message or two elements array:
     *           0 - path to the template file
     *           1 - template variables
     * @param array $headers String to be inserted at the end of the email header.
     * @param type $parameters Additional parameters.
     *
     * @return bool Returns TRUE if the mail was successfully accepted for delivery,
     *           FALSE otherwise.
     */
    public static function send($to, $subject, $message, $headers = [], $parameters = "") {
        if (is_array($message)) {
            $tplPath = $message[0];
            $tplVars = $message[1];

            $message = Content::render($tplPath, $tplVars);
        }

        return mail($to, $subject, $message, implode("\r\n", $headers), $parameters);
    }

    /**
     * Sends specific E-mail notification.
     *
     * @param string $email E-mail of the reciver.
     * @param string $subject Subject of the email to be sent.
     * @param string $template Name of the email message template file.
     * @param array $params List of the template parameters.
     * @param string $format E-mail format ("text" or "html").
     * @param string $replyTo Reply to e-mail address.
     *
     * @return bool Returns TRUE if the mail was successfully accepted for
     *           delivery, FALSE otherwise.
     */
    protected function sendNotification(
            $email, $subject, $languageCode, $template,
            array $params = null, $format = self::FORMAT_TEXT,
            $replyTo = ""
        ) {
        $_EMAIL = $params;

        ob_start();
        include($this->tplFolder . $languageCode . "/" . $template . ".tpl.php");
        if (!empty($this->signatureTpl)) {
            include($this->tplFolder . $languageCode . "/" . $this->signatureTpl);
        }
        $message = ob_get_contents();
        ob_end_clean();

        return self::sendMail($email, $subject, $message, $format, $replyTo);
    }

}

class EmailException extends \Exception
{}
