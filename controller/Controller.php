<?php
class Controller {
    private $_f3;
    private $dataLayer;

    function __construct($f3) {
        $this->_f3 = $f3;
        $this->dataLayer = new DataLayer();
    }

    public function run() {
        // Create a default route
        $this->_f3->route('GET /', $this->home(...));
        $this->_f3->route('GET|POST /form', $this->studentForm(...));
        $this->_f3->route('GET /schedule', $this->schedule(...));
        $this->_f3->route('GET /admin', $this->admin(...));

        $this->_f3->route('GET /api/courses', function($f3) {
            header("content-type: application/json");
            echo json_encode($this->dataLayer->getAllCourses());
        });

        $this->_f3->route('GET /api/prerequisites/@course', function($f3, $params) {
            header("content-type: application/json");
            echo json_encode($this->dataLayer->getPrerequisites($params['course']));
        });

        $this->_f3->route('GET /api/courses/@course', function($f3, $params) {
            header("content-type: application/json");
            echo json_encode($this->dataLayer->getCourseData($params['course']));
        });

        $this->_f3->route('PUT /api/courses/@course', function($f3, $params) {
            // Workaround to retrieve PUT request body, since PHP only provides GET and POST superglobals.
            $_PUT = json_decode(file_get_contents("php://input"), true);

            header("content-type: application/json");
            echo json_encode($this->dataLayer->updateCourse($_PUT));
        });

        // Run Fat-Free
        $this->_f3->run();
    }

    function home() {
        $view = new Template();
        $this->_f3->set('title', 'GRC Advisor');
        echo $view->render('view/home.html');
    }

    function studentForm() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $form = new StudentForm($_POST);

            if ($form->isValid()) {
                // TODO: Figure out how to send classes over SESSION.
                $_SESSION['preferences'] = $_POST;
                $this->_f3->reroute('/schedule');
            }
        } else {
            $form = new StudentForm([]);
        }

        $this->_f3->set("courses", $this->dataLayer->getAllCourses());
        $this->_f3->set("form", $form);

        $view = new Template();
        $this->_f3->set('title', 'Student Advising Form');
        echo $view->render('view/studentform.html');
    }

    function schedule() {
        $form = new StudentForm($_SESSION['preferences']);
        $schedule = new Schedule($form, $this->dataLayer);

        $this->_f3->set("schedule", $schedule->schedule);

        $view = new Template();
        echo $view->render('view/schedule.html');
    }

    function admin() {
        $this->_f3->set('title', 'Admin Dashboard');

        $view = new Template();
        echo $view->render('view/admin.html');
    }
}
