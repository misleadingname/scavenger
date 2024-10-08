<?php

namespace Scavenger\Internals\Exceptions;

use JetBrains\PhpStorm\Pure;
use Override;

class ScavengerDatabaseException extends ScavengerException
{
	#[Pure] #[Override]
	public function getFancyMessage(): string
	{
		return "<span><b>[Scavenger Database Error]</b> {$this->getMessage()}</span>";
	}
}