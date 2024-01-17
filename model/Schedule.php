<?php
class Schedule {
    public $schedule;

    const SEASONS = [
        "fall",
        "winter",
        "spring",
        "summer"
    ];

    private static function getCurrentMonthIndex() {
        $month = intval(date('n'));

        if ($month >= 1 && $month <= 3) {
            return 1; // Winter
        } else if ($month >= 10 && $month <= 12) {
            return 0; // Fall
        } else if ($month >= 4 && $month <= 6) {
            return 2; // Spring
        } else /*if ($month >= 7 && $month <= 9)*/ {
            return 3; // Summer
        }
    }

    /**
     * @param $form StudentForm
     */
    function __construct($form) {
        $priorCourses = $form->courses;
        $remainingCourses = array_diff(DataLayer::getRequiredCourse(), $priorCourses);

        $season = self::getCurrentMonthIndex();
        $year = intval(date('Y'));
        $this->schedule = [];

        while (count($remainingCourses) > 0) {
            // Find the courses that can be taken during the current quarter
            $possibleCourses = [];
            foreach ($remainingCourses as $course)
                if (DataLayer::canTakeCourse($course, $priorCourses))
                    array_push($possibleCourses, $course);

            // Sort possible courses according to how many prerequisites they will fulfill
            usort($possibleCourses, function($a, $b) {
                return DataLayer::requirementCount($b) - DataLayer::requirementCount($a);
            });

            // Choose the courses that fulfill the most prerequisites
            $quarterCourses = array_splice(
                $possibleCourses,
                0,
                min(count($possibleCourses), $form->coursesPerQuarter));

            // Add courses to the schedule
            $quarterName = self::SEASONS[$season] . $year;
            $this->schedule[$quarterName] = $quarterCourses;

            // Add courses to prior courses, remove them from remaining courses
            $priorCourses = array_merge($priorCourses, $quarterCourses);
            $remainingCourses = array_diff($remainingCourses, $quarterCourses);


            // Increment the current quarter
            if ($season == 0) $year++; // Fall -> Winter (December -> January)

            $season++;
            if ($season >= 4) $season = 0;
        }
    }
}