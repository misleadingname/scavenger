<?php

namespace App;

use Pecee\SimpleRouter\SimpleRouter;

define("PROJECT_ROOT", realpath(__DIR__ . "../../../"));

define("APP_ROOT", realpath(PROJECT_ROOT . "/App"));
define("SCAVENGER_ROOT", realpath(PROJECT_ROOT . "/Scavenger"));

session_start();

require_once(APP_ROOT . "/Header.php");
require_once(APP_ROOT . "/Router.php");

SimpleRouter::setDefaultNamespace("App\Controllers");
SimpleRouter::start();