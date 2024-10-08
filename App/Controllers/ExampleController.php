<?php

namespace App\Controllers;

use Override;
use Scavenger\Internals\Bases\BaseController;

class ExampleController extends BaseController
{
	#[Override] public static function index(): string
	{
		return render("Hello.twig",
			[
				"time" => date("d.m.Y H:i:s")
			]
		);
	}
}