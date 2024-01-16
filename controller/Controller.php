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
        } else {
            $form = new StudentForm([]);
        }

        $this->_f3->set("form", $form);

        $view = new Template();
        echo $view->render('view/studentform.html');
        echo $_SERVER['REQUEST_METHOD'];
    }
}