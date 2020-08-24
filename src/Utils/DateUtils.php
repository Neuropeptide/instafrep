<?php


namespace App\Utils;

/**
 * Class DateUtils
 * @package App\Utils
 *
 * Custom class to help with date handling. Feel free to add any useful methods !
 */
class DateUtils
{
    /**
     * Get the interval between two dates, and return a human friendly string
     * TODO: improve params order (if $date2 > $date1)
     * TODO: handle intl
     *
     * @param \DateTime $date1
     * @param \DateTime $date2
     * @return string
     */
    static public function getTimeBetween(\DateTime $date1, \DateTime $date2): string {

        $interval = $date1->diff($date2);

        if ($interval->d > 1) {
            return "il y a {$interval->d} jours";
        }

        if ($interval->d === 1) {
            return 'hier';
        }

        if ($interval->h > 1) {
            return "il y a {$interval->h} heures";
        }

        if ($interval->h === 1) {
            return 'il y a une heure';
        }

        if ($interval->i > 1) {
            return "il y a {$interval->i} minutes";
        }

        if ($interval->i === 1) {
            return 'il y a une minute';
        }

        if ($interval->s < 60) {
            return 'il y a quelques instants';
        }

        return "Impossible de comparer les dates :(";
    }

    public static function getTimeAgo($date) {
        $now = new \DateTime();
        return self::getTimeBetween($date, $now);
    }


}