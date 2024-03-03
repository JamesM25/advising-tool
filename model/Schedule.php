<?php
class Schedule {
    /**
     * @var array Associative array where the keys are quarters, and the values are arrays containing course IDs.
     */
    public $schedule;

    private $dataLayer;
    private $form;

    private $allCourses;
    private $remainingCourses;
    private $priorCourses;

    private static function removePriorCourses($remaining, $priorCourses) {
        $output = $remaining;

        foreach ($priorCourses as $prior) {
            $key = $prior['ID'];
            unset($output[$key]);
        }

        return $output;
    }

    private function comparePossibleCourses($a, $b) {
        $priorityCmp = $b['Priority'] - $a['Priority'];
        if ($priorityCmp != 0) return $priorityCmp;

        // If priority is the same, prefer courses that fulfill the most prerequisites
        return $b['RequiredFor'] - $a['RequiredFor'];
    }

    private static function isTechCourse($course) {
        return str_starts_with($course['Name'], "SDEV")
            || str_starts_with($course['Name'], "CS");
    }

    private function selectTopCourses($possibleCourses) {
        // Sort possible courses according to how many prerequisites they will fulfill
        // This way, it will prioritize courses which make the student eligible for more future courses.
        usort($possibleCourses, $this->comparePossibleCourses(...));

        $output = [];

        /*
         * Select a minimum number of tech courses
         * 3 classes per quarter: 2 tech, 1 gen-ed
         * 2 classes per quarter: 1-2 tech, 0-1 gen-ed
         */
        $minTechCourses = intval($this->form->coursesPerQuarter * 0.75);

        $techCourses = array_filter($possibleCourses, self::isTechCourse(...));

        while (count($techCourses) > 0 && count($output) < $minTechCourses) {
            $course = array_shift($techCourses);
            $output[] = $course;

            // Remove the course (and grouped courses) from the array of possible courses
            foreach ($possibleCourses as $key=>$value) {
                if ($value['ID'] == $course['ID']) {
                    unset($possibleCourses[$key]);
                    break;
                }
            }
        }

        while (count($output) < $this->form->coursesPerQuarter && count($possibleCourses) > 0) {
            $course = array_shift($possibleCourses);
            $output[] = $course;
        }

        return $output;
    }

    private function selectQuarterCourses() {
        // Find the courses that can be taken during the current quarter
        $priorCourseIDs = array_map(
            fn($course) => $course['ID'],
            $this->priorCourses
        );

        $possibleCourses = array_filter(
            $this->remainingCourses,
            fn($course) => $this->dataLayer->canTakeCourse($course['ID'], $priorCourseIDs)
        );

        return self::selectTopCourses($possibleCourses);
    }

    /**
     * @param $form StudentForm
     * @param $dataLayer DataLayer
     */
    function __construct($form, $dataLayer) {
        $this->dataLayer = $dataLayer;
        $this->form = $form;

        // All courses that must be completed before graduation
        $this->allCourses = [];
        foreach ($this->dataLayer->getAllCourses() as $course)
            $this->allCourses[$course['ID']] = $course;

        // Courses the student has already completed
        $this->priorCourses = [];
        foreach ($this->form->courses as $priorID)
            $this->priorCourses[] = $this->allCourses[$priorID];

        // Remaining courses that the student must complete before graduation
        $this->remainingCourses = self::removePriorCourses($this->allCourses, $this->priorCourses);

        // Get the current quarter
        $quarter = Quarter::current();

        // Academic plan
        $this->schedule = [];

        // If coursesPerQuarter is below 1, the while loop will never finish.
        if ($this->form->coursesPerQuarter < 1) return;

        $remainingAttempts = 100;

        while (count($this->remainingCourses) > 0) {
            // Don't include summer classes unless the summer checkbox was clicked
            if ($quarter->season == Quarter::SUMMER && !$this->form->summer) {
                $quarterCourses = [];
            } else {
                $quarterCourses = $this->selectQuarterCourses();

                // Add courses to prior courses, remove them from remaining courses
                $this->priorCourses = array_merge($this->priorCourses, $quarterCourses);
                $this->remainingCourses = self::removePriorCourses($this->remainingCourses, $quarterCourses);
            }

            // Add courses to the schedule
            $quarterName = $quarter->toString();
            $this->schedule[$quarterName] = array_map(
                fn($course) => $course['Name'],
                $quarterCourses
            );

            $quarter->increment();

            if ($remainingAttempts-- <= 0) {
                echo "An error occurred while processing the schedule";
                break;
            }
        }
    }
}