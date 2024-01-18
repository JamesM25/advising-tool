<?php
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
     * @return int Number of courses that require the given course
     */
    public static function requirementCount($course) {
        $sum = 0;

        foreach (self::PREREQUISITES as $key => $value)
            if ($value === $course) $sum++;

        return $sum;
    }
}