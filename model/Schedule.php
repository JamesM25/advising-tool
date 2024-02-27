<?php
class Schedule {
    /**
     * @var array Associative array where the keys are quarters, and the values are arrays containing course IDs.
     */
    public $schedule;

    private static function removeCourseGroup(&$courses, $group) {
        if ($group == null) return;

        $courses = array_filter($courses, function($course) use ($group) {
            return $course['GroupNum'] !== $group;
        });
    }

    private static function removePriorCourses($remaining, $priorCourses) {
        $output = $remaining;

        foreach ($priorCourses as $prior) {
            $key = $prior['ID'];
            $groupNum = $prior['GroupNum'];

            unset($output[$key]);
            self::removeCourseGroup($output, $groupNum);
        }

        return $output;
    }

    private static function getCourseGroupString($course, $allCourses) {
        $group = $course['GroupNum'];
        if ($group == null) return $course['Name'];

        $string = $course['Name'];
        foreach ($allCourses as $current) {
            if ($current['ID'] == $course['ID'] || $current['GroupNum'] != $course['GroupNum']) {
                continue;
            }

            $string .= " / " . $current['Name'];
        }

        return $string;
    }

    private static function selectCourses($possibleCourses, $max) {
        $output = [];

        /*
         * 3 classes per quarter: 2 tech, 1 gen-ed
         * 2 classes per quarter: 1-2 tech, 0-1 gen-ed
         */
        $minTechCourses = intval($max * 0.75);

        $techCourses = array_filter($possibleCourses, function($course) {
            return str_starts_with($course['Name'], "SDEV") || str_starts_with($course['Name'], "CS");
        });

        while (count($techCourses) > 0 && count($output) < $minTechCourses) {
            $course = array_shift($techCourses);
            $output[] = $course;
            self::removeCourseGroup($techCourses, $course['GroupNum']);
            self::removeCourseGroup($possibleCourses, $course['GroupNum']);

            foreach ($possibleCourses as $key=>$value) {
                if ($value['ID'] == $course['ID']) {
                    unset($possibleCourses[$key]);
                    break;
                }
            }
        }

        while (count($output) < $max && count($possibleCourses) > 0) {
            $course = array_shift($possibleCourses);
            $output[] = $course;
            self::removeCourseGroup($possibleCourses, $course['GroupNum']);
        }

        return $output;
    }

    /**
     * @param $form StudentForm
     * @param $dataLayer DataLayer
     */
    function __construct($form, $dataLayer) {
        // All courses that must be completed before graduation
        $allCourses = [];
        foreach ($dataLayer->getAllCourses() as $course)
            $allCourses[$course['ID']] = $course;

        // Courses the student has already completed
        $priorCourses = [];
        foreach ($form->courses as $priorID)
            $priorCourses[] = $allCourses[$priorID];

        // Remaining courses that the student must complete before graduation
        $remainingCourses = self::removePriorCourses($allCourses, $priorCourses);

        // Get the current quarter
        $quarter = Quarter::current();

        // Academic plan
        $this->schedule = [];

        // If coursesPerQuarter is below 1, the while loop will never finish.
        if ($form->coursesPerQuarter < 1) return;

        while (count($remainingCourses) > 0) {
            // Find the courses that can be taken during the current quarter
            $possibleCourses = [];

            $priorCourseIDs = array_map(
                function($course) { return $course['ID']; },
                $priorCourses
            );

            foreach ($remainingCourses as $course)
                if ($dataLayer->canTakeCourse($course['ID'], $priorCourseIDs))
                    $possibleCourses[] = $course;

            // Sort possible courses according to how many prerequisites they will fulfill
            // This way, it will prioritize courses which make the student eligible for more future courses.
            usort($possibleCourses, function($a, $b) use ($dataLayer) {
                return $dataLayer->getCoursePriority($b['ID']) - $dataLayer->getCoursePriority($a['ID']);
            });

            // Don't include summer classes unless the summer checkbox was clicked
            if ($quarter->season == Quarter::SUMMER && !$form->summer) {
                $quarterCourses = [];
            } else {
                $quarterCourses = self::selectCourses($possibleCourses, $form->coursesPerQuarter);
            }

            // Add courses to the schedule
            $quarterName = $quarter->toString();

            $this->schedule[$quarterName] = array_map(
                function($course) use ($allCourses) {
                    return self::getCourseGroupString($course, $allCourses);
                },
                $quarterCourses
            );

            // Add courses to prior courses, remove them from remaining courses
            $priorCourses = array_merge($priorCourses, $quarterCourses);
            $remainingCourses = self::removePriorCourses($remainingCourses, $quarterCourses);


            // Increment the current quarter
            $quarter->increment();
        }
    }
}