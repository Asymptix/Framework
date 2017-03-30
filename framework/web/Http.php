<?php

namespace Asymptix\web;

/**
 * Http protocol functionality and other connected tools.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2009 - 2017, Dmytro Zarezenko
 *
 * @git https://github.com/Asymptix/Framework
 * @license http://opensource.org/licenses/MIT
 */
class Http {

    const POST = "POST";
    const GET  = "GET";

    /**
     * Redirect to the given url.
     *
     * @param string $url The URL to redirect to.
     * @param array<mixed> $params Associative array of query parameters.
     * @param bool $session Whether to append session information.
     */
    public static function http_redirect($url, $params = [], $session = false) {
        $paramsString = "";
        foreach ($params as $key => $value) {
            $paramsString.= "&" . $key . "=" . $value;
        }
        if ($session) {
            $paramsString.= "&" . session_name() . "=" . session_id();
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
     * @param bool $serialize Serialize transmitted POST data values or not.
     */
    public static function httpRedirect($url = "", $postData = [], $serialize = true) {
        if (preg_match("#^http[s]?://.+#", $url)) { // absolute url
            if (function_exists("http_redirect")) {
                http_redirect($url);
            } else {
                self::http_redirect($url);
            }
        } else { // same domain (relative url)
            if (!empty($postData)) {
                if (is_array($postData)) {
                    if (!Session::exists('_post') || !is_array($_SESSION['_post'])) {
                        Session::set('_post', []);
                    }

                    foreach ($postData as $fieldName => $fieldValue) {
                        Session::set("_post[{$fieldName}]", $serialize ? serialize($fieldValue) : $fieldValue);
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
     * @param bool $serialize Serialize transmitted POST data values or not.
     */
    public static function redirect($url = "", $postData = [], $serialize = true) {
        self::httpRedirect($url, $postData, $serialize);
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
        }

        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * Returns HTTP referrer.
     *
     * @return string
     */
    public static function getReferrer() {
        return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "";
    }

    /**
     * Gets the address that the provided URL redirects to,
     * or FALSE if there's no redirect.
     *
     * @param string $url URL.
     *
     * @return mixed String with redirect URL or FALSE if no redirect.
     * @throws HttpException
     */
    public static function getRedirectUrl($url) {
        $urlParts = @parse_url($url);
        if (!$urlParts) {
            return false;
        }
        if (!isset($urlParts['host'])) { //can't process relative URLs
            return false;
        }

        if (!isset($urlParts['path'])) {
            $urlParts['path'] = '/';
        }

        $sock = fsockopen($urlParts['host'], (isset($urlParts['port']) ? (int) $urlParts['port'] : 80), $errno, $errstr, 30);
        if (!$sock) {
            throw new HttpException("$errstr ($errno)");
        }

        $request = "HEAD " . $urlParts['path'] . (isset($urlParts['query']) ? '?' . $urlParts['query'] : '') . " HTTP/1.1\r\n";
        $request.= 'Host: ' . $urlParts['host'] . "\r\n";
        $request.= "Connection: Close\r\n\r\n";
        fwrite($sock, $request);
        $response = '';
        while (!feof($sock)) {
            $response.= fread($sock, 8192);
        }
        fclose($sock);

        if (preg_match('/^Location: (.+?)$/m', $response, $matches)) {
            if (substr($matches[1], 0, 1) == "/") {
                return $urlParts['scheme'] . "://" . $urlParts['host'] . trim($matches[1]);
            }

            return trim($matches[1]);
        }

        return false;
    }

    /**
     * Follows and collects all redirects, in order, for the given URL.
     *
     * @param string $url
     * @return array
     */
    public static function getAllRedirects($url) {
        $redirects = [];
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
        }

        return $url;
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
        $postParams = [];
        foreach ($params as $key => &$val) {
            if (is_array($val)) {
                $val = implode(',', $val);
            }
            $postParams[] = $key . '=' . urlencode($val);
        }
        $postString = implode('&', $postParams);

        $parts = parse_url($url);

        $port = isset($parts['port']) ? (int)$parts['port'] : 80;

        $sock = fsockopen($parts['host'], $port, $errno, $errstr, $timeout);

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

        fwrite($sock, $request);

        $response = "";
        while (!feof($sock) && $result = fgets($sock)) {
            $response.= $result;
        }

        fclose($sock);

        list($respHeader, $respBody) = preg_split("/\R\R/", $response, 2);

        $headers = array_map(['self', "pair"], explode("\r\n", $respHeader));
        $headerList = [];
        foreach ($headers as $value) {
            $headerList[$value['key']] = $value['value'];
        }

        return [
            'request' => $request,
            'response' => [
                'header' => $respHeader,
                'headerList' => $headerList,
                'body' => trim(http_chunked_decode($respBody))
            ],
            'errno' => $errno,
            'errstr' => $errstr
        ];
    }

    /**
     * Validates URL existence with cURL request.
     *
     * @param string $url URL.
     *
     * @return bool
     */
    public static function urlExists($url) {
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    /**
     * Force HTTP status code.
     *
     * @param int $code Status code.
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
