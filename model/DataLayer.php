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
        $sql = "SELECT C.ID, C.Name, C.GroupNum, COUNT(P.PrerequisiteID) NumPrerequisites FROM Classes C LEFT JOIN Prerequisites P ON P.ClassID=C.ID GROUP BY C.ID";
        $sql = $this->_dbh->prepare($sql);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPrerequisites($courseId) {
        $sql = "SELECT * FROM Prerequisites WHERE ClassID = :id";
        $sql = $this->_dbh->prepare($sql);
        $sql->bindParam(":id", $courseId, PDO::PARAM_INT);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCourseData($courseId) {
        $sql = "SELECT * FROM Classes WHERE ID = :id";
        $sql = $this->_dbh->prepare($sql);
        $sql->bindParam(":id", $courseId, PDO::PARAM_INT);
        $sql->execute();

        $course = $sql->fetch(PDO::FETCH_ASSOC);

        $sql = "SELECT PrerequisiteID AS 'ID',GroupNum FROM Prerequisites WHERE ClassID = :id";
        $sql = $this->_dbh->prepare($sql);
        $sql->bindParam(":id", $courseId, PDO::PARAM_INT);
        $sql->execute();

        $course['Prerequisites'] = $sql->fetchAll(PDO::FETCH_ASSOC);

        return $course;
    }

    public function updateCourse($course) {
        $sql = "UPDATE Classes SET Name=:name, GroupNum=:group WHERE ID=:id";
        $sql = $this->_dbh->prepare($sql);
        $sql->bindParam(":name", $course["Name"], PDO::PARAM_STR);
        $sql->bindParam(":group", $course["GroupNum"], PDO::PARAM_INT);
        $sql->bindParam(":id", $course["ID"], PDO::PARAM_INT);
        return $sql->execute();

        // TODO: Prerequisites
    }
}