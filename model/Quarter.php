<?php
class Quarter {
    public const FALL = 0;
    public const WINTER = 1;
    public const SPRING = 2;
    public const SUMMER = 3;

    public const SEASONS = [
        "fall ",
        "winter ",
        "spring ",
        "summer "
    ];

    /**
     * @var int $season
     */
    public $season;

    /**
     * @var int $year
     */
    public $year;

    public function __construct($season, $year) {
        $this->season = $season;
        $this->year = $year;
    }

    public static function current() {
        $month = intval(date('n'));

        if ($month >= 1 && $month <= 3) {
            $season = self::WINTER;
        } else if ($month >= 10 && $month <= 12) {
            $season = self::FALL;
        } else if ($month >= 4 && $month <= 6) {
            $season = self::SPRING;
        } else /*if ($month >= 7 && $month <= 9)*/ {
            $season = self::SUMMER;
        }

        $year = intval(date('Y'));

        return new Quarter($season, $year);
    }

    public function increment() {
        if (++$this->season >= 4) $this->season = 0;

        // If we've gone from December (Fall) to January (Winter), the year should increment.
        if ($this->season == self::WINTER) $this->year++;
    }

    public function toString() {
        return self::SEASONS[$this->season] . $this->year;
    }
}