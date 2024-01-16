<?php

// Turn on error reporting
ini_set("display_errors", 1);
error_reporting(E_ALL);

require_once "vendor/autoload.php";

// Create the Fat-Free object
$f3 = Base::instance();
$controller = new Controller($f3);

// Create a default route
$f3->route('GET /', function () {
    $GLOBALS['controller']->home();
});

$f3->route('GET|POST /form', function () {
    $GLOBALS['controller']->studentForm();
});

// Run Fat-Free
$f3->run();