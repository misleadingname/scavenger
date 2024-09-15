<?php

namespace Scavenger\Internals\Bases;

use Exception;
use JetBrains\PhpStorm\NoReturn;
use Pecee\Http\Request;
use Pecee\SimpleRouter\Handlers\IExceptionHandler;

class BaseHttpExceptionHandler implements IExceptionHandler
{
	#[NoReturn] public function handleError(Request $request, Exception $error): void
	{

	}
}