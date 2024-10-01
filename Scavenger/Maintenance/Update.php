<?php
require_once("../../vendor/autoload.php");
require_once("./Helpers.php");

global $httpContext;

displayIntro();

fLog("Checking for updates...");

$releases = httpGet("https://api.github.com/repos/misleadingname/scavenger/releases");

print_r($releases);