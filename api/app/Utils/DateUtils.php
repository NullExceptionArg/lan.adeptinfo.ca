<?php

namespace App\Utils;

class DateUtils
{
    protected static $months = [
        'fr' => [
            'Janvier',
            'Février',
            'Mars',
            'Avril',
            'Mai',
            'Juin',
            'Juillet',
            'Août',
            'Septembre',
            'Octobre',
            'Novembre',
            'Décembre',
        ],
        'en' => [
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December',
        ],
    ];

    public static function getLocalizedMonth(int $month, string $language)
    {
        return self::$months[$language][$month - 1];
    }
}
