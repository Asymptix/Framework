<?php

namespace Asymptix\mail;

/**
 * Class for working with POP (Post Office Protocol).
 *
 * @category Asymptix PHP Framework
 *
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2010 - 2016, Dmytro Zarezenko
 *
 * @git https://github.com/Asymptix/Framework
 *
 * @license http://opensource.org/licenses/MIT
 */
class POP
{
    /**
     * Email regular expression.
     */
    const EMAIL_REGEX = '/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}/i';

    /**
     * Instance of the connection to the POP server.
     *
     * @var resource
     */
    public $connection = null;

    /**
     * List of the acceptable for a bounce emails.
     *
     * @var array
     */
    public $bouncedWhiteList = [];

    /**
     * List of the acceptable for a bounce email domains.
     *
     * @var array
     */
    public $bouncedWhiteDomains = [];

    /**
     * Open connection to POP server.
     *
     * @param string $host     POP server host (default: localhost).
     * @param int    $port     POP server port (default: 110)
     * @param string $username User name.
     * @param string $password User password.
     *
     * @throws POPServerException
     *
     * @return resource Connection to the POP server or throw POPServerException
     */
    public function open($host = 'localhost', $port = 110, $username = '', $password = '')
    {
        $sock = fsockopen($host, $port);

        if ($sock) {
            fgets($sock, 1024);

            // Connection accepted
            fwrite($sock, 'USER '.$username."\r\n");
            $userresponse = fgets($sock, 1024);
            if ($userresponse[0] == '+') { // User accepted
                fwrite($sock, 'PASS '.$password."\r\n");
                $passresponse = fgets($sock, 1024);
                if ($passresponse[0] != '+') { // Wrong password
                    $passresponse = str_replace("\r", '', str_replace("\n", '', $passresponse));

                    throw new POPServerException(
                        'Authentication not accepted. Incorrect password.', 1
                    );
                }
            } else { // Invalid username
                throw new POPServerException(
                    "Username '".$username."' not accepted.", 1
                );
            }
        } else {
            throw new POPServerException(
                'Unable to Connect to '.$host.'. Network Problems could be responsible.', 2
            );
        }
        $this->connection = $sock;

        return $sock;
    }

    /**
     * Close connection to the POP server.
     */
    public function close()
    {
        fwrite($this->connection, "QUIT\r\n");
        $quitresponse = fgets($this->connection, 1024);
        $quitresponse = '';
        fclose($this->connection);
    }

    /**
     * Delete message from POP server.
     *
     * @param int $messageId Id of the message.
     *
     * @return string Response string.
     */
    public function delete($messageId)
    {
        fwrite($this->connection, "DELE $messageId\r\n");

        return fgets($this->connection, 1024);
    }

    /**
     * Count messages in POP server.
     *
     * @return type
     */
    public function countMessages()
    {
        fwrite($this->connection, "STAT\r\n");
        $statresponse = fgets($this->connection, 1024);
        $avar = explode(' ', $statresponse);

        return (int) $avar[1];
    }

    /**
     * Return message header.
     *
     * @param int $messageNumber Number of the message.
     *
     * @return string
     */
    public function messageHeader($messageNumber)
    {
        fwrite($this->connection, "TOP $messageNumber 0\r\n");
        $buffer = '';
        $headerReceived = 0;
        while ($headerReceived == 0) {
            $temp = fgets($this->connection, 1024);
            $buffer .= $temp;
            if ($temp == ".\r\n") {
                $headerReceived = 1;
            }
        }

        return $buffer;
    }

    /**
     * Return parsed message header.
     *
     * @param int $messageNumber Number of the message.
     *
     * @return array
     */
    public function getParsedMessageHeader($messageNumber)
    {
        return $this->parseHeader(
            $this->messageHeader($messageNumber)
        );
    }

    /**
     * Return message by number.
     *
     * @param int $messageNumber Number of the message
     *
     * @return string
     */
    public function getMessage($messageNumber)
    {
        fwrite($this->connection, "RETR $messageNumber\r\n");
        $headerReceived = 0;
        $buffer = '';
        while ($headerReceived == 0) {
            $temp = fgets($this->connection, 1024);
            $buffer .= $temp;
            if (substr($buffer, 0, 4) === '-ERR') {
                return false;
            }
            if ($temp == ".\r\n") {
                $headerReceived = 1;
            }
        }

        return $buffer;
    }

    /**
     * Return list of the messages in the POP server mail box.
     *
     * @return array<string>
     */
    public function getMessages()
    {
        $messages = [];

        for ($i = 1; ; $i++) {
            $message = $this->getMessage($i);
            if ($message === false) {
                break;
            }

            $messages[] = $message;
        }

        return $messages;
    }

    /**
     * Return list of bounced e-mail addresses.
     *
     * @return array<string>
     */
    public function getBouncedEmails($delete = true, $number = null)
    {
        $emails = [];

        for ($i = 1; (is_null($number) ? true : $i <= $number); $i++) {
            $message = $this->getMessage($i);
            if ($message !== false) {
                $markers = [
                    'The following address(es) failed:',
                    'Delivery to the following recipients failed permanently:',
                    'Delivery to the following recipients failed.',
                    'Delivery to the following recipient has been delayed:',
                    'The following message to <',
                    'This is a permanent error; I\'ve given up. Sorry it didn\'t work out.',
                    'I\'m sorry to have to inform you that your message could not',
                    'This Message was undeliverable due to the following reason:',
                    'Delivery has failed to these recipients or groups:',
                    '------Transcript of session follows -------',
                    'Sorry, we were unable to deliver your message to the following address.',
                    'Your message cannot be delivered to the following recipients:',
                    'We have tried to deliver your message, but it was rejected by the recipient',
                    'These recipients of your message have been processed by the mail server:',
                ];
                $failSignaturePos = false;
                for ($q = 0; $failSignaturePos === false && $q < count($markers); $q++) {
                    $failSignaturePos = strpos($message, $markers[$q]);
                }

                $endSignaturePos = strpos($message, '--', $failSignaturePos); //End Position
                if ($failSignaturePos === false/* || $endSignaturePos === false*/) {
                    /*if ($delete) {
                        $this->delete($i);
                    }*/
                    continue;
                } else {
                    if ($endSignaturePos === false || $endSignaturePos <= $failSignaturePos) {
                        $endSignaturePos = strlen($message);
                    }
                    preg_match_all(
                        self::EMAIL_REGEX, substr($message, $failSignaturePos, $endSignaturePos - $failSignaturePos), $emailData
                    );

                    $emails = $this->filterBouncedEmails($emailData);

                    if ($delete) {
                        $this->delete($i);
                    }
                }
            } else {
                break;
            }
        }

        return array_unique($emails);
    }

    /**
     * Return list of e-mail addresses except white lists.
     *
     * @return array<string>
     */
    public function getEmails($delete = true, $number = null)
    {
        $emails = [];

        for ($i = 1; (is_null($number) ? true : $i <= $number); $i++) {
            $message = $this->getMessage($i);
            if ($message !== false) {
                $failSignaturePos = 0;
                $endSignaturePos = strlen($message);

                preg_match_all(
                    self::EMAIL_REGEX, substr($message, $failSignaturePos, $endSignaturePos - $failSignaturePos), $emailData
                );

                $emails = $this->filterBouncedEmails($emailData);

                if ($delete) {
                    $this->delete($i);
                }
            } else {
                break;
            }
        }

        return array_unique($emails);
    }

    /**
     * Returns only bounced e-mails from matches.
     *
     * @param array $emailData Matches array after preg_math_all funciton.
     *
     * @return array<string> List of bounced e-mails.
     */
    private function filterBouncedEmails($emailData)
    {
        $emails = [];

        if (isset($emailData[0])) {
            if (is_array($emailData[0])) {
                foreach ($emailData[0] as $email) {
                    if ($this->isBouncedEmail($email)) {
                        $emails[] = $email;
                    }
                }
            } else {
                $email = $emailData[0];
                if ($this->isBouncedEmail($email)) {
                    $emails[] = $email;
                }
            }
        }

        return $emails;
    }

    /**
     * Detects if email is bounced.
     *
     * @param string $email Email address.
     *
     * @return bool
     */
    private function isBouncedEmail($email)
    {
        $email = trim($email);
        if (!empty($email)) {
            if (in_array($email, $this->bouncedWhiteList)) {
                return false;
            }
            $domain = substr(strrchr($email, '@'), 1);
            if (in_array($domain, $this->bouncedWhiteDomains)) {
                return false;
            }

            return true;
        }

        return false;
    }

    /**
     * Parse message header.
     *
     * @param string $header Header of the message.
     *
     * @return array
     */
    public function parseHeader($header)
    {
        $avar = explode("\n", $header);
        $len = count($avar);
        $ret = $L2 = $L3 = null;
        for ($i = 0; $i < $len; $i++) {
            if (isset($avar[$i]) && isset($avar[$i][0]) && isset($avar[$i][1]) && isset($avar[$i][2])) {
                $L2 = $avar[$i][0].$avar[$i][1];
                $L3 = $avar[$i][0].$avar[$i][1].$avar[$i][2];
                if ($L2 != '  ' && $L3 != 'Rec' && $L2 != '') {
                    $avar2 = explode(':', $avar[$i]);
                    $temp = str_replace("$avar2[0]:", '', $avar[$i]);
                    $ret[$avar2[0]] = $temp;
                }
            }
        }

        return $ret;
    }

    /**
     * Get a unique-id listing for one or all messages.
     *
     * @return array
     */
    public function uniqueListing()
    {
        fwrite($this->connection, "UIDL\r\n");
        $response = '';

        while (true) {
            $response .= fgets($this->connection, 1024);
            if (substr($response, -3) === ".\r\n") {
                $response = substr($response, 0, strlen($response) - 3); //the ".\r\n" removed
                break;
            }
        }
        $em = explode("\r\n", $response);
        $em2 = [];
        foreach ($em as $q) {
            if (strpos($q, ' ') !== false) {
                $t = explode(' ', $q);
                $em2[] = $t[1];
            }
        }

        return $em2;
    }
}

class POPServerException extends \Exception
{
}
