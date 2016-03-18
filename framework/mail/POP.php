<?php

namespace Asymptix\mail;

/**
 * Class for working with POP (Post Office Protocol).
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2010 - 2016, Dmytro Zarezenko
 *
 * @git https://github.com/Asymptix/Framework
 * @license http://opensource.org/licenses/MIT
 */
class POP {
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
    public $bouncedWhiteList = array();

    /**
     * List of the acceptable for a bounce email domains.
     *
     * @var array
     */
    public $bouncedWhiteDomains = array();

    /**
     * Open connection to POP server.
     *
     * @param string $host POP server host (default: localhost).
     * @param integer $port POP server port (default: 110)
     * @param string $username User name.
     * @param string $password User password.
     *
     * @return resource Connection to the POP server or throw POPServerException
     * @throws POPServerException
     */
    public function open($host = "localhost", $port = 110, $username = "", $password = "") {
        $sh = fsockopen($host, $port);

        if ($sh) {
            $banner = fgets($sh, 1024);

            // Connection accepted
            fputs($sh, "USER " . $username . "\r\n");
            $userresponse = fgets($sh, 1024);
            if ($userresponse[0] == "+") { // User accepted
                fputs($sh, "PASS " . $password . "\r\n");
                $passresponse = fgets($sh, 1024);
                if ($passresponse[0] != "+") { // Wrong password
                    $passresponse = str_replace("\r", "", str_replace("\n", "", $passresponse));

                    throw new POPServerException(
                        "Authentication not accepted. Incorrect password.", 1
                    );
                }
            } else { // Invalid username
                throw new POPServerException(
                    "Username '" . $username . "' not accepted.", 1
                );
            }
        } else {
            throw new POPServerException(
                "Unable to Connect to " . $host . ". Network Problems could be responsible.", 2
            );
        }
        $this->connection = $sh;
        return $sh;
    }

    /**
     * Close connection to the POP server.
     */
    public function close() {
        fputs($this->connection, "QUIT\r\n");
        $quitresponse = fgets($this->connection, 1024);
        $quitresponse = "";
        fclose($this->connection);
    }

    /**
     * Delete message from POP server.
     *
     * @param integer $messageId Id of the message.
     *
     * @return string Response string.
     */
    public function delete($messageId) {
        fputs($this->connection, "DELE $messageId\r\n");

        return fgets($this->connection, 1024);
    }

    /**
     * Count messages in POP server.
     *
     * @return type
     */
    public function countMessages() {
        fputs($this->connection, "STAT\r\n");
        $statresponse = fgets($this->connection, 1024);
        $avar = explode(" ", $statresponse);

        return (integer)$avar[1];
    }

    /**
     * Return message header.
     *
     * @param integer $messageNumber Number of the message.
     *
     * @return string
     */
    public function messageHeader($messageNumber) {
        fputs($this->connection, "TOP $messageNumber 0\r\n");
        $buffer = "";
        $header_received = 0;
        while( $header_received == 0 ) {
            $temp = fgets( $this->connection, 1024 );
            $buffer .= $temp;
            if( $temp == ".\r\n" ) {
                $header_received = 1;
            }
        }
        return $buffer;
    }

    /**
     * Return parsed message header.
     *
     * @param integer $messageNumber Number of the message.
     *
     * @return array
     */
    public function getParsedMessageHeader($messageNumber) {
        return $this->parseHeader(
            $this->messageHeader($messageNumber)
        );
    }

    /**
     * Return message by number.
     *
     * @param integer $messageNumber Number of the message
     *
     * @return string
     */
    public function getMessage($messageNumber) {
        fputs($this->connection, "RETR $messageNumber\r\n");
        $header_received = 0;
        $buffer = "";
        while ($header_received == 0) {
            $temp = fgets($this->connection, 1024);
            $buffer .= $temp;
            if (substr($buffer, 0, 4) === '-ERR') {
                return false;
            }
            if ($temp == ".\r\n") {
                $header_received = 1;
            }
        }
        return $buffer;
    }

    /**
     * Return list of the messages in the POP server mail box.
     *
     * @return array<string>
     */
    public function getMessages() {
        $messages = array();

        for ($i=1; ; $i++) {
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
    public function getBouncedEmails($delete = true, $number = null) {
        $emails = array();

        for ($i = 1; (is_null($number) ? true : $i <= $number) ; $i++) {
            $message = $this->getMessage($i);
            if ($message !== false) {
                $markers = array(
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
                    'These recipients of your message have been processed by the mail server:'
                );
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
                        '/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}/i', substr($message, $failSignaturePos, $endSignaturePos - $failSignaturePos), $emailData
                    );
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
    public function getEmails($delete = true, $number = null) {
        $emails = array();

        for ($i = 1; (is_null($number) ? true : $i <= $number) ; $i++) {
            $message = $this->getMessage($i);
            if ($message !== false) {
                $failSignaturePos = 0;
                $endSignaturePos = strlen($message);

                preg_match_all(
                    '/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}/i', substr($message, $failSignaturePos, $endSignaturePos - $failSignaturePos), $emailData
                );
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
     * Detects if email is bounced.
     *
     * @param string $email Email address.
     *
     * @return boolean
     */
    private function isBouncedEmail($email) {
        $email = trim($email);
        if (!empty($email)) {
            if (in_array($email, $this->bouncedWhiteList)) {
                return false;
            }
            $domain = substr(strrchr($email, "@"), 1);
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
    public function parseHeader($header) {
        $avar = explode("\n", $header);
        $len = count($avar);
        $ret = $L2 = $L3 = NULL;
        for ($i = 0; $i < $len; $i++) {
            if( isset( $avar[$i] ) && isset( $avar[$i][0] ) && isset( $avar[$i][1] ) && isset( $avar[$i][2] ) ){
                $L2 = $avar[$i][0] . $avar[$i][1];
                $L3 = $avar[$i][0] . $avar[$i][1] . $avar[$i][2];
                if ($L2 != "  " && $L3 != "Rec" && $L2 != "") {
                    $avar2 = explode(":", $avar[$i]);
                    $temp = str_replace("$avar2[0]:", "", $avar[$i]);
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
    public function uniqueListing() {
        fputs($this->connection, "UIDL\r\n");
        $r = ''; //Response

        while (true) {
            $r .= fgets($this->connection, 1024);
            if (substr($r, -3) === ".\r\n") {
                $r = substr($r, 0, strlen($r) - 3); //the ".\r\n" removed
                break;
            }
        }
        $em = explode("\r\n", $r);
        $em2 = array();
        foreach ($em as $q) {
            if (strpos($q, ' ') !== false) {
                $t = explode(' ', $q);
                $em2[] = $t[1];
            }
        }
        return $em2;
    }
}

class POPServerException extends \Exception {}