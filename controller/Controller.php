<?php
class Controller {
    private $_f3;

    function __construct($f3) {
        $this->_f3 = $f3;
    }

    function home()
    {
        $view = new Template();
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

        $this->_f3->set("form", $form);

        $view = new Template();
        echo $view->render('view/studentform.html');
    }

    function schedule() {
        $form = new StudentForm($_SESSION['preferences']);
        $schedule = new Schedule($form);

        $this->_f3->set("schedule", $schedule->schedule);

        $view = new Template();
        echo $view->render('view/schedule.html');
    }
}
