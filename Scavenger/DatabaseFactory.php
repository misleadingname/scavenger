<?php

namespace Scavenger;

use Scavenger\Internals\Databases\MySQLDatabase;
use Scavenger\Internals\Databases\SQLiteDatabase;
use Scavenger\Internals\ScavengerException;

class DatabaseFactory
{
	/**
	 * Creates a new database connection according to the ini configuration.
	 *
	 * Databases need to be enabled and use a valid database type or else the factory will always throw a ScavengerException.
	 * @throws ScavengerException
	 */
	public static function createDatabase(): MySQLDatabase|SQLiteDatabase
	{
		if (!SCAVENGER_CONFIG["Database"]["Enabled"]) {
			throw new ScavengerException("Cannot create a database while databases are disabled.");
		}

		$databaseType = SCAVENGER_CONFIG["Database"]["Type"];

		return match ($databaseType) {
			"mysql" => new MySQLDatabase(),
			"sqlite" => new SQLiteDatabase(),
			default => throw new ScavengerException("Invalid database type, \"$databaseType\"."),
		};
	}
}