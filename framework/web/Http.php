<?php

namespace Asymptix\web;

/**
 * Http protocol functionality and other connected tools.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2009 - 2016, Dmytro Zarezenko
 *
 * @git https://github.com/Asymptix/Framework
 * @license http://opensource.org/licenses/MIT
 */
class Http {

    const POST = "POST";
    const GET = "GET";

    /**
     * Redirect to the given url.
     *
     * @param string $url The URL to redirect to.
     * @param array<mixed> $params Associative array of query parameters.
     * @param boolean $session Whether to append session information.
     */
    public static function http_redirect($url, $params = array(), $session = false) {
        $paramsString = "";
        foreach ($params as $key => $value) {
            $paramsString .= "&" . $key . "=" . $value;
        }
        if ($session) {
            $paramsString .= "&" . session_name() . "=" . session_id();
        }
        $paramsString = substr($paramsString, 1);
        if ($paramsString) {
            $paramsString = "?" . $paramsString;
        }
        header("Location: " . $url . $paramsString);
        exit();
    }

    /**
     * Perform HTTP redirect with saving POST params in session.
     *
     * @param string $url URL redirect to.
     * @param array<mixed> $postData List of post params to save.
     */
    public static function httpRedirect($url = "", $postData = array()) {
        if (preg_match("#^http[s]?://.+#", $url)) { // absolute url
            if (function_exists("http_redirect")) {
                http_redirect($url);
            } else {
                self::http_redirect($url);
            }
        } else { // same domain (relative url)
            if (!empty($postData)) {
                if (is_array($postData)) {
                    if (!isset($_SESSION['_post']) || !is_array($_SESSION['_post'])) {
                        $_SESSION['_post'] = array();
                    }

                    foreach ($postData as $fieldName => $fieldValue) {
                        $_SESSION['_post'][$fieldName] = serialize($fieldValue);
                    }
                } else {
                    throw new HttpException("Wrong POST data.");
                }
            }
            if (function_exists("http_redirect")) {
                http_redirect("http://" . $_SERVER['SERVER_NAME'] . "/" . $url);
            } else {
                self::http_redirect("http://" . $_SERVER['SERVER_NAME'] . "/" . $url);
            }
        }
    }

    /**
     * Perform HTTP redirect with saving POST params in session.
     *
     * @param string $url URL redirect to.
     * @param array<mixed> $postData List of post params to save.
     */
    public static function redirect($url = "", $postData = array()) {
        self::httpRedirect($url, $postData);
    }

    /**
     * Returns clients IP-address.
     *
     * @return string
     */
    public static function getIP() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) { //check ip from share internet
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) { //to check ip is pass from proxy
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'];
        }
        return "";
    }

    /**
     * Gets the address that the provided URL redirects to,
     * or FALSE if there's no redirect.
     *
     * @param string $url
     * @return mixed String with redirect URL or FALSE if no redirect.
     */
    public static function getRedirectUrl($url) {
        $url_parts = @parse_url($url);
        if (!$url_parts) {
            return false;
        }
        if (!isset($url_parts['host'])) { //can't process relative URLs
            return false;
        }

        if (!isset($url_parts['path'])) {
            $url_parts['path'] = '/';
        }

        $sock = fsockopen($url_parts['host'], (isset($url_parts['port']) ? (int) $url_parts['port'] : 80), $errno, $errstr, 30);
        if (!$sock) {
            return false;
        }

        $request = "HEAD " . $url_parts['path'] . (isset($url_parts['query']) ? '?' . $url_parts['query'] : '') . " HTTP/1.1\r\n";
        $request .= 'Host: ' . $url_parts['host'] . "\r\n";
        $request .= "Connection: Close\r\n\r\n";
        fwrite($sock, $request);
        $response = '';
        while (!feof($sock)) {
            $response.= fread($sock, 8192);
        }
        fclose($sock);

        if (preg_match('/^Location: (.+?)$/m', $response, $matches)) {
            if (substr($matches[1], 0, 1) == "/") {
                return $url_parts['scheme'] . "://" . $url_parts['host'] . trim($matches[1]);
            } else {
                return trim($matches[1]);
            }
        } else {
            return false;
        }
    }

    /**
     * Follows and collects all redirects, in order, for the given URL.
     *
     * @param string $url
     * @return array
     */
    public static function getAllRedirects($url) {
        $redirects = array();
        while ($newurl = self::getRedirectUrl($url)) {
            if (in_array($newurl, $redirects)) {
                break;
            }
            $redirects[] = $newurl;
            $url = $newurl;
        }

        return $redirects;
    }

    /**
     * Gets the address that the URL ultimately leads to.
     * Returns $url itself if it isn't a redirect.
     *
     * @param string $url
     * @return string
     */
    public static function getFinalUrl($url) {
        $redirects = self::getAllRedirects($url);
        if (count($redirects) > 0) {
            return array_pop($redirects);
        } else {
            return $url;
        }
    }

    /**
     * Executes CURL async request.
     *
     * @param string $url URL.
     * @param array $params List of request params.
     * @param string $type Type of the request (GET, POST, ...).
     * @param int $timeout Timeout in seconds.
     *
     * @return type
     */
    public static function curlRequestAsync($url, $params, $type = self::POST, $timeout = 30) {
        $postParams = array();
        foreach ($params as $key => &$val) {
            if (is_array($val)) {
                $val = implode(',', $val);
            }
            $postParams[] = $key . '=' . urlencode($val);
        }
        $postString = implode('&', $postParams);

        $parts = parse_url($url);

        $port = isset($parts['port']) ? (integer)$parts['port'] : 80;

        $fp = fsockopen($parts['host'], $port, $errno, $errstr, $timeout);

        // Data goes in the path for a GET request
        if ($type == self::GET) {
            $parts['path'].= '?' . $postString;
        }

        $request = "$type " . $parts['path'] . " HTTP/1.1\r\n";
        $request.= "Host: " . $parts['host'] . "\r\n";

        if ($type == self::POST) {
            $request.= "Content-Type: application/x-www-form-urlencoded\r\n";
            $request.= "Content-Length: " . strlen($postString) . "\r\n";
        }
        $request.= "Connection: Close\r\n";
        $request.= "\r\n";

        // Data goes in the request body for a POST request
        if ($type == self::POST && isset($postString)) {
            $request.= $postString;
        }

        fwrite($fp, $request);

        $response = "";
        while (!feof($fp) && $result = fgets($fp)) {
            $response.= $result;
        }

        fclose($fp);

        list($respHeader, $respBody) = preg_split("/\R\R/", $response, 2);

        $headers = array_map(array('self', "pair"), explode("\r\n", $respHeader));
        $headerList = array();
        foreach ($headers as $value) {
            $headerList[$value['key']] = $value['value'];
        }

        return array(
            'request' => $request,
            'response' => array(
                'header' => $respHeader,
                'headerList' => $headerList,
                'body' => trim(http_chunked_decode($respBody))
            ),
            'errno' => $errno,
            'errstr' => $errstr
        );
    }

    /**
     * Force HTTP status code.
     *
     * @param integer $code Status code.
     * @param string $path Include path if needed.
     *
     * @throws HttpException If invalid HTTP status provided.
     */
    public static function forceHttpStatus($code, $path = null) {
        switch ($code) {
            //TODO: implement other statuses
            case (404):
                header('HTTP/1.0 404 Not Found', true, 404);
                break;
            default:
                throw new HttpException("Invalid HTTP status code '" . $code . "'");
        }
        if (!is_null($path)) {
            include($path);
        }
        exit();
    }

    /**
     * Magic methods:
     *    force404($path = null) - force HTTP status codes.
     *
     * @param string $name The name of the method being called.
     * @param type $arguments Enumerated array containing the parameters passed
     *           to the method.
     * @return mixed
     *
     * @throws HttpException If invalid method name provided.
     */
    public static function __callStatic($name, $arguments) {
        if (substr($name, 0, 5) === "force") {
            $code = (int)substr($name, 5);
            $path = isset($arguments[0]) ? $arguments[0] : null;

            return self::forceHttpStatus($code, $path);
        }
        throw new HttpException("Invalid HTTP class method '" . $name . "'");
    }

}

/**
 * Service exception class.
 */
class HttpException extends \Exception {}