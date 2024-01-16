<?php
class Schedule {
    public $schedule;

    const SEASONS = [
        "fall",
        "winter",
        "spring",
        "summer"
    ];

    static function getCurrentMonthIndex() {
        $month = intval(date('n'));

        if ($month >= 1 && $month <= 3) {
            return 1; // Winter
        } else if ($month >= 10 && $month <= 12) {
            return 0; // Fall
        } else if ($month >= 4 && $month <= 6) {
            return 2; // Spring
        } else if ($month >= 7 && $month <= 9) {
            return 3; // Summer
        }
    }

    /**
     * @param $form StudentForm
     */
    function __construct($form) {
        $requiredCourses = DataLayer::getRequiredCourse();

        //shuffle($requiredCourses);

        $visited = $form->courses;

        $queue = [];
        $topsort = [];

        do {
            while (!empty($queue)) array_push($topsort, array_shift($queue));

            for ($i = 0; $i < count($requiredCourses); $i++) {
                $course = $requiredCourses[$i];
                if (in_array($course, $visited)) continue;

                $prerequisite = DataLayer::getPrerequisite($course);
                if (empty($prerequisite) || in_array($prerequisite, $visited)) {
                    array_push($queue, $course);
                    array_push($visited, $course);
                }
            }
        } while (!empty($queue));


        $season = self::getCurrentMonthIndex();
        $year = intval(date('Y'));

        $this->schedule = [];

        // TODO: There should be no quarters where one class requires another class being taken during the same quarter
        while (!empty($topsort)) {
            $quarterName = self::SEASONS[$season] . $year;
            $quarterCourses = [];
            for ($i = 0; $i < $form->coursesPerQuarter && !empty($topsort); $i++) {
                // add quarter to schedule
                array_push($quarterCourses, array_shift($topsort));
            }

            $this->schedule[$quarterName] = $quarterCourses;

            // Fall to Winter
            if ($season == 0) $year++;

            $season++;
            if ($season >= 4) $season = 0;
        }
    }
}