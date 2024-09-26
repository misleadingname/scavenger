<?php

namespace App;

/* Your initialization goes under here... */

use Scavenger\Internals\Bases\BaseDatabase;
use Scavenger\Internals\ScavengerException;

require_once(APP_ROOT . "/Helpers.php");

try {
	$d = new BaseDatabase();
} catch (ScavengerException $e) {
	die($e->getFancyMessage());
}
