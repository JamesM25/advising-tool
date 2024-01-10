<?php
class Controller {
    private $_f3;

    function constructor($f3) {
        $this->_f3 = $f3;
    }

    function home()
    {
        $view = new Template();
        echo $view->render('view/home.html');
    }
}