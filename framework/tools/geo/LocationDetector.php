<?php

namespace Asymptix\tools\geo;

/**
 * Location detection functionality. Provides functionality to detect location
 * by IP address with using 3rd party services.
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
class LocationDetector
{
    const PROVIDER_IPINFO_IO = 'http://ipinfo.io/[IP]/json';
    const PROVIDER_GEOPLUGIN_NET = 'http://www.geoplugin.net/json.gp?ip=[IP]';
    const PROVIDER_FREEGEOIP_IO = 'http://freegeoip.io/json/[IP]';
    const TYPE_FULL_OBJECT = 0;
    const TYPE_FULL_ARRAY = 1;
    const TYPE_LOCATION = 2;
    const TYPE_ADDRESS = 3;
    const TYPE_CITY = 4;
    const TYPE_STATE = 5;
    const TYPE_REGION = 6;
    const TYPE_COUNTRY = 7;
    private static $continents = [
        'AF' => 'Africa',
        'AN' => 'Antarctica',
        'AS' => 'Asia',
        'EU' => 'Europe',
        'OC' => 'Australia (Oceania)',
        'NA' => 'North America',
        'SA' => 'South America',
    ];

    private $provider = null;

    public function __construct($provider = self::PROVIDER_GEOPLUGIN_NET)
    {
        $this->provider = $provider;
    }

    public function get($ip, $type)
    {
        if (is_null($this->provider)) {
            throw new LocationDetectorException('Invalid data provider.');
        }

        return self::_get($ip, $type, $this->provider);
    }

    public static function _get($ip, $type, $provider)
    {
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            throw new LocationDetectorException('Invalid IP address.');
        }

        $response = file_get_contents(str_replace('[IP]', $ip, $provider));
        if ($response === false) {
            throw new LocationDetectorException("Can't receive response from data provider.");
        }

        $geoData = @json_decode($response);
        switch ($provider) {
            case self::PROVIDER_GEOPLUGIN_NET:
                if (@strlen(trim($geoData->geoplugin_countryCode)) == 2) {
                    switch ($type) {
                        case self::TYPE_FULL_OBJECT:
                            return $geoData;
                        case self::TYPE_FULL_ARRAY:
                            return @json_decode($response, true);
                        case self::TYPE_LOCATION:
                            return [
                                'city'           => @$geoData->geoplugin_city,
                                'state'          => @$geoData->geoplugin_regionName,
                                'country'        => @$geoData->geoplugin_countryName,
                                'country_code'   => @$geoData->geoplugin_countryCode,
                                'continent'      => @self::$continents[strtoupper($geoData->geoplugin_continentCode)],
                                'continent_code' => @$geoData->geoplugin_continentCode,
                            ];
                        case self::TYPE_ADDRESS:
                            $address = [$geoData->geoplugin_countryName];
                            if (@strlen($geoData->geoplugin_regionName) >= 1) {
                                $address[] = $geoData->geoplugin_regionName;
                            }
                            if (@strlen($geoData->geoplugin_city) >= 1) {
                                $address[] = $geoData->geoplugin_city;
                            }

                            return implode(', ', array_reverse($address));
                        case self::TYPE_CITY:
                            return @$geoData->geoplugin_city;
                        case self::TYPE_STATE:
                        case self::TYPE_REGION:
                            return @$geoData->geoplugin_regionName;
                        case self::TYPE_COUNTRY:
                            return [
                                'country'      => @$geoData->geoplugin_countryName,
                                'country_code' => @$geoData->geoplugin_countryCode,
                            ];
                        default:
                            throw new LocationDetectorException('Invalid return type.');
                    }
                }

                return;

            case self::PROVIDER_IPINFO_IO:
                if (@strlen(trim($geoData->country)) == 2) {
                    switch ($type) {
                        case self::TYPE_FULL_OBJECT:
                            return $geoData;
                        case self::TYPE_FULL_ARRAY:
                            return @json_decode($response, true);
                        default:
                            throw new LocationDetectorException('Invalid return type.');
                    }
                }

                return;

            case self::PROVIDER_FREEGEOIP_IO:
                if (@strlen(trim($geoData->country_code)) == 2) {
                    switch ($type) {
                        case self::TYPE_FULL_OBJECT:
                            return $geoData;
                        case self::TYPE_FULL_ARRAY:
                            return @json_decode($response, true);
                        default:
                            throw new LocationDetectorException('Invalid return type.');
                    }
                }

                return;
        }
    }
}

class LocationDetectorException extends \Exception
{
}
