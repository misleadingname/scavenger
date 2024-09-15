<?php

namespace App\ExceptionHandlers;

use Exception;
use Pecee\Http\Request;
use Scavenger\Internals\Bases\BaseHttpExceptionHandler;

class ExampleHttpExceptionHandler extends BaseHttpExceptionHandler
{
	#[NoReturn] public function handleError(Request $request, Exception $error): void
	{
		$code = $error->getCode();
		if ($code == 0) {
			$code = 404;
		}

		http_response_code($code);
		die("{$error->getCode()}: {$error->getMessage()}");
	}
}