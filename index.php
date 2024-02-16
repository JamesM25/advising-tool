<?php
session_start();

// Turn on error reporting
ini_set("display_errors", 1);
error_reporting(E_ALL);

require_once "vendor/autoload.php";

// Create the Fat-Free object
$f3 = Base::instance();
$controller = new Controller($f3);

$controller->run();