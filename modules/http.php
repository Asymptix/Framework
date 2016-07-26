<?php

/**
 * HTTP functions declaration.
 *
 * @category Asymptix PHP Framework
 *
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2009-2015, Dmytro Zarezenko
 *
 * @git https://github.com/Asymptix/Framework
 *
 * @license http://opensource.org/licenses/MIT
 */
if (!function_exists('http_redirect')) {
    /**
     * Redirect to the given url.
     *
     * @param string       $url     The URL to redirect to.
     * @param array<mixed> $params  Associative array of query parameters.
     * @param bool         $session Whether to append session information.
     */
    function http_redirect($url, $params = [], $session = false)
    {
        $paramsString = '';
        foreach ($params as $key => $value) {
            $paramsString .= '&'.$key.'='.$value;
        }
        if ($session) {
            $paramsString .= '&'.session_name().'='.session_id();
        }
        $paramsString = substr($paramsString, 1);
        if ($paramsString) {
            $paramsString = '?'.$paramsString;
        }
        header('Location: '.$url.$paramsString);
        exit();
    }
}

if (!function_exists('http_response_code')) {
    /**
     * Get or Set the HTTP response code.
     *
     * @param int $code The optional response_code will set the response code.
     *
     * @return int The current response code. By default the return value is int(200).
     */
    function http_response_code($code = null)
    {
        if ($code !== null) {
            switch ($code) {
                case 100: $text = 'Continue';
                    break;
                case 101: $text = 'Switching Protocols';
                    break;
                case 200: $text = 'OK';
                    break;
                case 201: $text = 'Created';
                    break;
                case 202: $text = 'Accepted';
                    break;
                case 203: $text = 'Non-Authoritative Information';
                    break;
                case 204: $text = 'No Content';
                    break;
                case 205: $text = 'Reset Content';
                    break;
                case 206: $text = 'Partial Content';
                    break;
                case 300: $text = 'Multiple Choices';
                    break;
                case 301: $text = 'Moved Permanently';
                    break;
                case 302: $text = 'Moved Temporarily';
                    break;
                case 303: $text = 'See Other';
                    break;
                case 304: $text = 'Not Modified';
                    break;
                case 305: $text = 'Use Proxy';
                    break;
                case 400: $text = 'Bad Request';
                    break;
                case 401: $text = 'Unauthorized';
                    break;
                case 402: $text = 'Payment Required';
                    break;
                case 403: $text = 'Forbidden';
                    break;
                case 404: $text = 'Not Found';
                    break;
                case 405: $text = 'Method Not Allowed';
                    break;
                case 406: $text = 'Not Acceptable';
                    break;
                case 407: $text = 'Proxy Authentication Required';
                    break;
                case 408: $text = 'Request Time-out';
                    break;
                case 409: $text = 'Conflict';
                    break;
                case 410: $text = 'Gone';
                    break;
                case 411: $text = 'Length Required';
                    break;
                case 412: $text = 'Precondition Failed';
                    break;
                case 413: $text = 'Request Entity Too Large';
                    break;
                case 414: $text = 'Request-URI Too Large';
                    break;
                case 415: $text = 'Unsupported Media Type';
                    break;
                case 500: $text = 'Internal Server Error';
                    break;
                case 501: $text = 'Not Implemented';
                    break;
                case 502: $text = 'Bad Gateway';
                    break;
                case 503: $text = 'Service Unavailable';
                    break;
                case 504: $text = 'Gateway Time-out';
                    break;
                case 505: $text = 'HTTP Version not supported';
                    break;
                default:
                    exit('Unknown http status code "'.htmlentities($code).'"');
                    break;
            }

            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');

            header($protocol.' '.$code.' '.$text);

            $GLOBALS['http_response_code'] = $code;
        } else {
            $code = (isset($GLOBALS['http_response_code']) ? $GLOBALS['http_response_code'] : 200);
        }

        return $code;
    }
}

if (!function_exists('http_chunked_decode')) {
    /**
     * Dechunk an http 'transfer-encoding: chunked' message.
     *
     * @param string $chunk the encoded message
     *
     * @return string the decoded message.
     *                If $chunk wasn't encoded properly it will be returned unmodified.
     */
    function http_chunked_decode($chunk)
    {
        $pos = 0;
        $len = strlen($chunk);
        $dechunk = null;

        while (($pos < $len) && ($chunkLenHex = substr($chunk, $pos, ($newlineAt = strpos($chunk, "\n", $pos + 1)) - $pos))) {
            if (!is_hex($chunkLenHex)) {
                trigger_error('Value is not properly chunk encoded', E_USER_WARNING);

                return $chunk;
            }

            $pos = $newlineAt + 1;
            $chunkLen = hexdec(rtrim($chunkLenHex, "\r\n"));
            $dechunk .= substr($chunk, $pos, $chunkLen);
            $pos = strpos($chunk, "\n", $pos + $chunkLen) + 1;
        }

        return $dechunk;
    }

    /**
     * Determine if a string can represent a number in hexadecimal.
     *
     * @param string $hex
     *
     * @return bool true if the string is a hex, otherwise false
     */
    function is_hex($hex)
    {
        $hex = strtolower(trim(ltrim($hex, '0')));
        if (empty($hex)) {
            $hex = 0;
        }
        $dec = hexdec($hex);

        return $hex == dechex($dec);
    }
}
