<?php

namespace Scavenger\Internals\Exceptions;

use Exception;
use JetBrains\PhpStorm\Pure;
use Throwable;

class ScavengerException extends Exception
{
	#[Pure] public function __construct(string $message = "", ?Throwable $previous = null)
	{
		parent::__construct($message, 424, $previous);
	}

	#[Pure]
	public function getFancyMessage(): string
	{
		return "<span><b>[Scavenger Error]</b> {$this->getMessage()}</span>";
	}
}