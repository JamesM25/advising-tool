<?php
class StudentForm {
    public $courses;

    public $coursesPerQuarter;

    public $summer;

    public $notes;

    private $_isValid;

    public const MAX_NOTES_LENGTH = 2000;



    /**
     * @param $data array Associative array containing the form data
     */
    public function __construct($data) {
        $this->_isValid = true;

        if (!empty($data)) {
            $this->courses = $data['courses'] ?? [];
            $this->coursesPerQuarter = $data['num-courses'] ?? "";
            $this->summer = $data['summer'] ?? false;
            $this->notes = trim($data['notes']);

            if ($this->coursesPerQuarter < 1 || strlen($this->notes) > self::MAX_NOTES_LENGTH) {
                $this->_isValid = false;
            }
        } else {
            // Defaults
            $this->courses = [];
            $this->coursesPerQuarter = "";
            $this->summer = false;
            $this->notes = "";

            $this->_isValid = false;
        }

        //if (isset($))
    }

    public function isValid() {
        return $this->_isValid;
    }
}