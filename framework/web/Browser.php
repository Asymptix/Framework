<?php

namespace Asymptix\web;

/**
 * Browser detection functionality and other connected tools.
 *
 * @category Asymptix PHP Framework
 *
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2016, Dmytro Zarezenko
 *
 * @git https://github.com/Asymptix/Framework
 *
 * @license http://opensource.org/licenses/MIT
 */
class Browser
{
    /**
     * Browser detection RegEx pattern.
     *
     * @var type
     */
    private static $pattern;

    /**
     * Returns full browser detection information.
     *
     * @return array
     */
    public static function getInfo()
    {
        return [
            'browser'    => self::getBrowserInfo(),
            'platform'   => self::getPlatformInfo(),
            'user_agent' => self::getUserAgent(),
            'pattern'    => self::$pattern,
        ];
    }

    /**
     * Returns platform (OS) information.
     *
     * @return array Array of data or null.
     */
    public static function getPlatformInfo()
    {
        $platformName = $platformVersion = null;
        $userAgent = self::getUserAgent();

        if (empty($userAgent)) {
            return;
        }

        /*
         * Detect platform (operation system)
         */
        if (preg_match('/linux/i', $userAgent)) {
            $platformName = 'linux';
        } elseif (preg_match('/macintosh|mac os x/i', $userAgent)) {
            $platformName = 'mac';
        } elseif (preg_match('/windows|win32/i', $userAgent)) {
            $platformName = 'windows';
        }
        $platformFragment = substr($userAgent, stripos($userAgent, $platformName) + strlen($platformName) + 1);
        $platformFragments = explode(';', $platformFragment);
        if (isset($platformFragments[0])) {
            $platformVersion = $platformFragments[0];
        }

        return [
            'name'    => $platformName,
            'version' => $platformVersion,
        ];
    }

    /**
     * Returns browser information.
     *
     * @return array Array of data or null.
     */
    public static function getBrowserInfo()
    {
        $browserFullName = $browserShortName = $browserVersion = null;
        $userAgent = self::getUserAgent();

        if (empty($userAgent)) {
            return;
        }

        // Browser identifiers
        $identifiers = ['Version', 'other'];

        // Detect browser
        if (preg_match('/MSIE|Trident/i', $userAgent) && !preg_match('/Opera|OPR/i', $userAgent)) {
            $browserFullName = 'Internet Explorer';
            $browserShortName = 'MSIE';
            $identifiers[] = 'Trident';
        } elseif (preg_match('/Firefox/i', $userAgent)) {
            $browserFullName = 'Mozilla Firefox';
            $browserShortName = 'Firefox';
        } elseif (preg_match('/Chrome/i', $userAgent) && !preg_match('/Opera|OPR/i', $userAgent)) {
            $browserFullName = 'Google Chrome';
            $browserShortName = 'Chrome';
        } elseif (preg_match('/Safari|AppleWebKit/i', $userAgent) && !preg_match('/Opera|OPR/i', $userAgent)) {
            $browserFullName = 'Apple Safari';
            $browserShortName = 'Safari';
        } elseif (preg_match('/Opera|OPR/i', $userAgent)) {
            $browserFullName = 'Opera';
            $browserShortName = 'Opera';
            $identifiers[] = 'OPR';
        } elseif (preg_match('/Netscape/i', $userAgent)) {
            $browserFullName = 'Netscape';
            $browserShortName = 'Netscape';
        }

        /*
         * Detect browser version number
         */
        $identifiers[] = $browserShortName;
        self::$pattern = '#(?<browser>'.implode('|', $identifiers).
                ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all(self::$pattern, $userAgent, $matches)) {
            // we have no matching number just continue
        }

        // see how many we have
        $chunks = count($matches['browser']);
        if ($chunks > 1) { //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if (strripos($userAgent, 'Version') < strripos($userAgent, $browserShortName)
              && isset($matches['version'][0])) {
                $browserVersion = $matches['version'][0];
            } elseif (isset($matches['version'][1])) {
                $browserVersion = $matches['version'][1];
            }
        } elseif (isset($matches['version'][0])) {
            $browserVersion = $matches['version'][0];
        }

        // New IE version detection
        if (isset($matches['browser'][0]) && $matches['browser'][0] == 'Trident') {
            $browserVersion = sprintf('%.1f', (int) $browserVersion + 4);
        }

        // check if we have a number
        if (empty($browserVersion)) {
            $browserVersion = null;
        }

        return [
            'full_name'  => $browserFullName,
            'short_name' => $browserShortName,
            'version'    => $browserVersion,
        ];
    }

    /**
     * Returns HTTP User Agent.
     *
     * @return string
     */
    public static function getUserAgent()
    {
        return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    }
}
