<?php

namespace App;

use Pecee\Http\Middleware\BaseCsrfVerifier;

use App\Controllers\ExampleController;
use App\ExceptionHandlers\ExampleHttpExceptionHandler;
use Pecee\SimpleRouter\SimpleRouter;

/* Your routes go under here... */

SimpleRouter::csrfVerifier(new BaseCsrfVerifier());

SimpleRouter::group([
	"exceptionHandler" => ExampleHttpExceptionHandler::class,
], function () {
	SimpleRouter::get("/", [ExampleController::class, "index"]);
});