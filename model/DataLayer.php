<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/../db_advising.php';

class DataLayer {
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
     * @param $course int a course ID
     * @param $priorCourses string[] of course IDs, e.g. [ "math97", "eng101", "sdev106" ]
     * @return boolean
     */
    public function canTakeCourse($course, $priorCourses) {
        $prerequisites = $this->getPrerequisites($course);

        if (count($prerequisites) == 0) return true; // No prerequisites

        foreach ($priorCourses as $prior) {

            foreach ($prerequisites as $key=>$prerequisite) {

                if ($prerequisite['PrerequisiteID'] == $prior) {
                    $groupNum = $prerequisite['GroupNum'];

                    unset($prerequisites[$key]);

                    if ($groupNum !== null) {
                        $prerequisites = array_filter(
                            $prerequisites,
                            function ($prereq) use ($groupNum) {
                                return $prereq['GroupNum'] != $groupNum;
                            }
                        );
                    }

                    break;
                }
            }

        }

        return count($prerequisites) == 0;
    }

    /**
     * @param $course int course ID
     * @return int priority value
     */
    public function getCoursePriority($course) {
        $sql = "SELECT COUNT(ClassID) FROM Prerequisites WHERE PrerequisiteID = :id";
        $sql = $this->_dbh->prepare($sql);
        $sql->bindParam(":id", $course, PDO::PARAM_INT);
        $sql->execute();

        return $sql->fetch()[0];
    }


    public function getAllCourses() {
        $sql = "SELECT * FROM Classes";
        $sql = $this->_dbh->prepare($sql);
        $sql->execute();
        return $sql->fetchAll();
    }

    public function getPrerequisites($courseId) {
        $sql = "SELECT * FROM Prerequisites WHERE ClassID = :id";
        $sql = $this->_dbh->prepare($sql);
        $sql->bindParam(":id", $courseId, PDO::PARAM_INT);
        $sql->execute();
        return $sql->fetchAll();
    }
}