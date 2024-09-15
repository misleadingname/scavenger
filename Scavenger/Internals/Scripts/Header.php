<?php

namespace Scavenger\Internals\Scripts;

use Scavenger\Internals\ScavengerException;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

if(!file_exists(PROJECT_ROOT . "/config.ini")) {
	$error = "Couldn't find the \"/config.ini\" file.";

	print("Scavenger error: $error");
	throw new ScavengerException($error);
}
define("SCAVENGER_CONFIG", parse_ini_file(PROJECT_ROOT . "/config.ini"));

if(!SCAVENGER_CONFIG) {
	$error = "Malformed \"/config.ini\" file.";

	print("Scavenger error: $error");
	throw new ScavengerException($error);
}

require_once(PROJECT_ROOT . "/vendor/autoload.php");

$loader = new FilesystemLoader(APP_ROOT . "/Templates");
$twig = new Environment($loader);
$twig->enableAutoReload();

$twig->addFunction(new TwigFunction("csrf_token", function () {
	return csrf_token();
}));