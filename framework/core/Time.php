<?php

namespace Asymptix\core;

/**
 * Enhanced time functions.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2017, Dmytro Zarezenko
 *
 * @git https://github.com/Asymptix/Framework
 * @license http://opensource.org/licenses/MIT
 */
class Time {

    const HUMAN_FORMAT_DEFAULT = "human_format_default";
    const HUMAN_FORMAT_WITH_ZEROS = "human_format_with_zeros";
    const HUMAN_FORMAT_TOP_ONLY = "human_format_top_only";

    /**
     * Converts seconds time period to the human readable time string.
     *
     * @param int $inputSeconds Time period in seconds.
     * @param string $format Format.
     *
     * @return string
     */
    public static function secondsToTime($inputSeconds, $format = self::HUMAN_FORMAT_DEFAULT) {
        $secondsInAMinute = 60;
        $secondsInAnHour = 60 * $secondsInAMinute;
        $secondsInADay = 24 * $secondsInAnHour;

        // Extract days
        $days = floor($inputSeconds / $secondsInADay);

        // Extract hours
        $hourSeconds = $inputSeconds % $secondsInADay;
        $hours = floor($hourSeconds / $secondsInAnHour);

        // Extract minutes
        $minuteSeconds = $hourSeconds % $secondsInAnHour;
        $minutes = floor($minuteSeconds / $secondsInAMinute);

        // Extract the remaining seconds
        $remainingSeconds = $minuteSeconds % $secondsInAMinute;
        $seconds = ceil($remainingSeconds);

        // Format and return
        $timeParts = [];
        $sections = [
            'day' => (int)$days,
            'hour' => (int)$hours,
            'minute' => (int)$minutes,
            'second' => (int)$seconds,
        ];

        foreach ($sections as $name => $value){
            if ($value > 0 || $format === self::HUMAN_FORMAT_WITH_ZEROS) {
                $timeParts[] = $value . ' ' . $name . ($value == 1 ? '' : 's');

                if ($format === self::HUMAN_FORMAT_TOP_ONLY && $value > 0) {
                    break;
                }
            }
        }

        return implode(', ', $timeParts);
    }

}
