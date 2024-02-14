<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/../db_advising.php';

class DataLayer {
    private const PREREQUISITES = [
        "cs108/cs109" => "math97",
        "math141/math147" => "math97",
        "math146/math256" => "math97",
        "eng126/eng127/eng128/eng235" => "eng101",
        "sdev106" => "math97",
        "sdev121" => "cs108/cs109",
        "sdev117" => "sdev106",
        "sdev218" => "math97",
        "sdev219" => "sdev218",
        "sdev220" => "sdev219",
    ];

    private $_dbh;

    function __construct() {
        try {
            $this->_dbh = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
        }
        catch(PDOException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @return string[] Array of course IDs required to graduate
     */
    public static function getRequiredCourse(): array {
        return array(
            "math97",
            "math141/math147",
            "math146/math256",
            "eng101",
            "eng126/eng127/eng128/eng235",
            "cmst210/cmst220/cmst230/cmst238",
            "Lab Science",
            "cs108/cs109",
            "sdev106",
            "sdev121",
            "sdev117",
            "sdev218",
            "sdev219",
            "sdev220",
            "sdev201",
            "sdev280"
        );
    }

    /**
     * @param $course string a course ID, e.g. "sdev117"
     * @param $priorCourses string[] of course IDs, e.g. [ "math97", "eng101", "sdev106" ]
     * @return boolean
     */
    public static function canTakeCourse($course, $priorCourses) {
        $prerequisite = self::PREREQUISITES[$course] ?? "";

        if (empty($prerequisite)) return true; // No prerequisites

        return in_array($prerequisite, $priorCourses);
    }

    /**
     * @param $course string course ID
     * @return bool True if the given course is a technical (CS or SDEV) course.
     */
    public static function isTechCourse($course) {
        return str_starts_with($course, "sdev") || str_starts_with($course, "cs");
    }

    /**
     * @param $course string course ID
     * @return int Number of courses that require the given course
     */
    private static function requirementCount($course) {
        $sum = 0;

        foreach (self::PREREQUISITES as $key => $value)
            if ($value === $course) $sum++;

        return $sum;
    }

    /**
     * @param $course string course ID
     * @return int priority value
     */
    public static function getCoursePriority($course) {
        $priority = self::requirementCount($course);

        // sdev280 should be taken towards end of the program.
        if ($course === "sdev280") $priority -= 1000;

        return $priority;
    }

    public static function getAllCourses() {
        return array(
            [
                "id" => 1,
                "name" => "math97",
                "group" => null
            ],
            [
                "id" => 2,
                "name" => "sdev201",
                "group" => null
            ],
            [
                "id" => 3,
                "name" => "sdev280",
                "group" => null
            ],
            [
                "id" => 4,
                "name" => "math141",
                "group" => 1
            ],
            [
                "id" => 5,
                "name" => "math146",
                "group" => 1
            ],
            [
                "id" => 6,
                "name" => "sdev218",
                "group" => null
            ],
        );
    }

    public static function getPrerequisites($courseId) {
        $courses = self::getAllCourses();

        foreach ($courses as $course) {
            if ($course['id'] == $courseId) {
                $prereq = self::PREREQUISITES[$course['name']];
                if (empty($prereq)) return [];

                return [ $prereq ];
            }
        }

        return [];
    }
}