<?php
class StudentForm {
    public $courses;
    public $coursesPerQuarter;
    public $summer;
    private $_isValid;

    // Array to hold prerequisite courses for each course
    private $prerequisites;

    /**
     * @param $data array Associative array containing the form data
     * @param $prerequisites array Associative array containing prerequisites for each course
     */
    public function __construct($data, $prerequisites) {
        $this->_isValid = true;
        $this->prerequisites = $prerequisites;

        if (!empty($data)) {
            $this->courses = $data['courses'] ?? [];
            $this->coursesPerQuarter = $data['num-courses'] ?? "";
            $this->summer = $data['summer'] ?? false;

            if ($this->coursesPerQuarter < 1) {
                $this->_isValid = false;
            } else {
                // Check prerequisites
                foreach ($this->courses as $course) {
                    if (!$this->hasPrerequisitesFulfilled($course)) {
                        $this->_isValid = false;
                        break;
                    }
                }
            }
        } else {
            // Defaults
            $this->courses = [];
            $this->coursesPerQuarter = "";
            $this->summer = false;
            $this->_isValid = false;
        }
    }

    public function isValid() {
        return $this->_isValid;
    }

    // Check if prerequisites for a course are fulfilled
    private function hasPrerequisitesFulfilled($course) {
        if (!isset($this->prerequisites[$course])) {
            // No prerequisites defined for this course
            return true;
        }

        foreach ($this->prerequisites[$course] as $prerequisite) {
            if (!in_array($prerequisite, $this->courses)) {
                // Prerequisite not fulfilled
                return false;
            }
        }

        return true;
    }
}
