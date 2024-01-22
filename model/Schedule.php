<?php
class Schedule {
    /**
     * @var array Associative array where the keys are quarters, and the values are arrays containing course IDs.
     */
    public $schedule;

    const FALL = 0;
    const WINTER = 1;
    const SPRING = 2;
    const SUMMER = 3;

    const SEASONS = [
        "fall ",
        "winter ",
        "spring ",
        "summer "
    ];

    private static function getCurrentMonthIndex() {
        $month = intval(date('n'));

        if ($month >= 1 && $month <= 3) {
            return self::WINTER;
        } else if ($month >= 10 && $month <= 12) {
            return self::FALL;
        } else if ($month >= 4 && $month <= 6) {
            return self::SPRING;
        } else /*if ($month >= 7 && $month <= 9)*/ {
            return self::SUMMER;
        }
    }

    /**
     * @param $form StudentForm
     */
    function __construct($form) {
        // Courses the student has already completed
        $priorCourses = $form->courses;

        // All courses the student must still complete before they can graduate
        $remainingCourses = array_diff(DataLayer::getRequiredCourse(), $priorCourses);

        // Get the current quarter
        $season = self::getCurrentMonthIndex();
        $year = intval(date('Y'));

        // Academic plan
        $this->schedule = [];

        // If coursesPerQuarter is below 1, the while loop will never finish.
        if ($form->coursesPerQuarter < 1) return;

        while (count($remainingCourses) > 0) {

            // Find the courses that can be taken during the current quarter
            $possibleCourses = [];
            foreach ($remainingCourses as $course)
                if (DataLayer::canTakeCourse($course, $priorCourses))
                    array_push($possibleCourses, $course);

            // Sort possible courses according to how many prerequisites they will fulfill
            // This way, it will prioritize courses which make the student eligible for more future courses.
            usort($possibleCourses, function($a, $b) {
                return DataLayer::requirementCount($b) - DataLayer::requirementCount($a);
            });

            // Don't include summer classes unless the summer checkbox was clicked
            if ($season == self::SUMMER && !$form->summer) {
                $quarterCourses = [];
            }
            else {
                // Choose the courses that fulfill the most prerequisites
                $quarterCourses = array_splice(
                    $possibleCourses,
                    0,
                    min(count($possibleCourses), $form->coursesPerQuarter));
            }

            // Add courses to the schedule
            $quarterName = self::SEASONS[$season] . $year;
            $this->schedule[$quarterName] = $quarterCourses;

            // Add courses to prior courses, remove them from remaining courses
            $priorCourses = array_merge($priorCourses, $quarterCourses);
            $remainingCourses = array_diff($remainingCourses, $quarterCourses);


            // Increment the current quarter
            if ($season == self::FALL) $year++; // Fall -> Winter (December -> January)

            $season++;
            if ($season >= 4) $season = 0;
        }
    }
}