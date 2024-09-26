<?php

namespace Scavenger\Internals;

use JetBrains\PhpStorm\Pure;
use Throwable;

class ScavengerException extends \Exception
{
	#[Pure] public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
	{
		parent::__construct($message, 424, $previous);
	}

	public function getFancyMessage(): string
	{
		return "<span><b>[Scavenger Error]</b> {$this->getMessage()}</span>";
	}
}