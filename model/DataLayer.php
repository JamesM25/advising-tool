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
     * @param $course int Course ID
     * @param $priorCourses int[] of course IDs
     * @return boolean
     */
    public function canTakeCourse($course, $priorCourses) {
        $prerequisites = $this->getPrerequisites($course);

        if (count($prerequisites) == 0) return true; // No prerequisites

        foreach ($priorCourses as $prior) {
            foreach ($prerequisites as $key=>$prerequisite) {
                if ($prerequisite == $prior) {
                    unset($prerequisites[$key]);
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
        $sql = "SELECT ID, Name, Priority FROM Classes";
        $sql = $this->_dbh->prepare($sql);
        $sql->execute();
        $courses = $sql->fetchAll(PDO::FETCH_ASSOC);

        for ($i = 0; $i < count($courses); $i++) {
            $courses[$i]['Prerequisites'] = $this->getPrerequisites($courses[$i]['ID']);
        }

        return $courses;
    }

    public function getPrerequisites($courseId) {
        $sql = "SELECT PrerequisiteID FROM Prerequisites WHERE ClassID = :id";
        $sql = $this->_dbh->prepare($sql);
        $sql->bindParam(":id", $courseId, PDO::PARAM_INT);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getCourseData($courseId) {
        $sql = "SELECT * FROM Classes WHERE ID = :id";
        $sql = $this->_dbh->prepare($sql);
        $sql->bindParam(":id", $courseId, PDO::PARAM_INT);
        $sql->execute();

        $course = $sql->fetch(PDO::FETCH_ASSOC);

        if ($course != null) {
            $sql = "SELECT PrerequisiteID AS 'ID' FROM Prerequisites WHERE ClassID = :id";
            $sql = $this->_dbh->prepare($sql);
            $sql->bindParam(":id", $courseId, PDO::PARAM_INT);
            $sql->execute();

            $course['Prerequisites'] = $sql->fetchAll(PDO::FETCH_ASSOC);
        }

        return $course;
    }

    private function updateCoursePrerequisites($course) {
        $sql = "DELETE FROM Prerequisites WHERE ClassID = :id";
        $sql = $this->_dbh->prepare($sql);
        $sql->bindParam(":id", $course['ID'], PDO::PARAM_INT);
        $sql->execute();

        foreach ($course['Prerequisites'] as $prerequisite) {
            $sql = "INSERT INTO Prerequisites (PrerequisiteID, ClassID) VALUES (:prerequisite, :class)";
            $sql = $this->_dbh->prepare($sql);
            $sql->bindParam(":prerequisite", $prerequisite, PDO::PARAM_INT);
            $sql->bindParam(":class", $course['ID'], PDO::PARAM_INT);
            $sql->execute();
        }
    }

    public function updateCourse($course) {
        $sql = "UPDATE Classes SET Name=:name, Priority=:priority WHERE ID=:id";
        $sql = $this->_dbh->prepare($sql);
        $sql->bindParam(":name", $course["Name"], PDO::PARAM_STR);
        $sql->bindParam(":priority", $course["Priority"], PDO::PARAM_INT);
        $sql->bindParam(":id", $course["ID"], PDO::PARAM_INT);

        // TODO: Prerequisites
        $result = $sql->execute();


        $this->updateCoursePrerequisites($course);



        return $this->getCourseData($course["ID"]);
    }

    public function addCourse($course) {
        $sql = "INSERT INTO Classes (Name, Priority) VALUES (:name, :priority)";
        $sql = $this->_dbh->prepare($sql);
        $sql->bindParam(":name", $course['Name'], PDO::PARAM_STR);
        $sql->bindParam(":priority", $course['Priority'], PDO::PARAM_INT);
        $sql->execute();

        $course['ID'] = $this->_dbh->lastInsertId();

        $this->updateCoursePrerequisites($course);

        return $this->getCourseData($course['ID']);
    }

    public function deleteCourseByID($id) {
        $sql = "DELETE FROM Classes WHERE ID=:id";
        $sql = $this->_dbh->prepare($sql);
        $sql->bindParam(":id", $id, PDO::PARAM_INT);
        $sql->execute();

        $sql = "DELETE FROM Prerequisites WHERE ClassID=:id OR PrerequisiteID=:id";
        $sql = $this->_dbh->prepare($sql);
        $sql->bindParam(":id", $id, PDO::PARAM_INT);
        $sql->execute();
    }
}