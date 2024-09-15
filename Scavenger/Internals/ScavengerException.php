<?php

namespace Scavenger\Internals;

use JetBrains\PhpStorm\Pure;
use Throwable;

class ScavengerException extends \Exception
{
	#[Pure] public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
	{
		parent::__construct("[Scavenger Error] $message", 424, $previous);
	}

}