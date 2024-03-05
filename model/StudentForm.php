<?php
class StudentForm {
    public $courses;

    public $coursesPerQuarter;

    public $summer;

    private $_isValid;



    /**
     * @param $data array Associative array containing the form data
     */
    public function __construct($data) {
        $this->_isValid = true;

        if (!empty($data)) {
            $this->courses = $data['courses'] ?? [];
            $this->coursesPerQuarter = $data['num-courses'] ?? "";
            $this->summer = $data['summer'] ?? false;

            if ($this->coursesPerQuarter < 1) $this->_isValid = false;
        } else {
            // Defaults
            $this->courses = [];
            $this->coursesPerQuarter = "";
            $this->summer = false;

            $this->_isValid = false;
        }

        //if (isset($))
    }

    public function isValid() {
        return $this->_isValid;
    }
}