<?php

namespace App;

use App\Controllers\ExampleController;
use App\ExceptionHandlers\ExampleHttpExceptionHandler;
use Pecee\Http\Middleware\BaseCsrfVerifier;
use Pecee\SimpleRouter\SimpleRouter;

/* Your routes go under here... */

SimpleRouter::csrfVerifier(new BaseCsrfVerifier());

SimpleRouter::group([
	"exceptionHandler" => ExampleHttpExceptionHandler::class,
], function () {
	SimpleRouter::get("/", [ExampleController::class, "index"]);
});