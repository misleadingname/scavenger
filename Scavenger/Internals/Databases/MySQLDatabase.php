<?php

namespace Scavenger\Internals\Databases;

use Scavenger\Internals\Bases\BaseDatabase;
use Scavenger\Internals\ScavengerException;

class MySQLDatabase extends BaseDatabase
{
	// TODO: Create this class.

	/**
	 * @throws ScavengerException
	 */
	public function __construct(string $host = SCAVENGER_CONFIG["Database"]["Host"],
	                            string $user = SCAVENGER_CONFIG["Database"]["Username"],
	                            string $password = SCAVENGER_CONFIG["Database"]["Password"],
	                            string $database = SCAVENGER_CONFIG["Database"]["Database"])
	{
		throw new ScavengerException("Not implemented");
	}
}