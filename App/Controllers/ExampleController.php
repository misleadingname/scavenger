<?php

namespace App\Controllers;

use Override;
use Scavenger\Internals\Bases\BaseController;
use function Scavenger\Internals\Scripts\Helpers\render;

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