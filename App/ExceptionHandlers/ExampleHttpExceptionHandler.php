<?php

namespace App\ExceptionHandlers;

use Exception;
use JetBrains\PhpStorm\NoReturn;
use Pecee\Http\Request;
use Scavenger\Internals\Bases\BaseHttpExceptionHandler;
use Scavenger\Internals\Exceptions\ScavengerException;

class ExampleHttpExceptionHandler extends BaseHttpExceptionHandler
{
	#[NoReturn] public function handleError(Request $request, Exception $error): void
	{
		if (is_a($error, ScavengerException::class)) {
			die($error->getFancyMessage());
		}

		$code = $error->getCode();
		if ($code == 0) {
			$code = 404;
		}

		http_response_code($code);
		die("{$error->getCode()}: {$error->getMessage()}");
	}
}