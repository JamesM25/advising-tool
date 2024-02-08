<?php
session_start();

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

$f3->route('GET /schedule', function () {
    $GLOBALS['controller']->schedule();
});

$f3->route('GET /admin', function () {
    $GLOBALS['controller']->admin();
});

$f3->route('GET /api/courses', function($f3) {
    header("content-type: application/json");
    echo json_encode(DataLayer::getAllCourses());
});

$f3->route('GET /api/prerequisites/@course', function($f3, $params) {
    header("content-type: application/json");
    echo json_encode(DataLayer::getPrerequisites($params['course']));
});

// Run Fat-Free
$f3->run();