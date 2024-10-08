<?php

namespace Scavenger\Internals\Databases;

use PDO;
use PDOException;
use Scavenger\Internals\Bases\BaseDatabase;
use Scavenger\Internals\Exceptions\ScavengerDatabaseException;
use Scavenger\Internals\Exceptions\ScavengerException;

class SQLiteDatabase extends BaseDatabase
{
	/**
	 * @throws ScavengerException
	 */
	public function __construct(string $location = PROJECT_ROOT . "/" . SCAVENGER_CONFIG["SQLite"]["Location"])
	{
		try {
			$this->pdoConnection = new PDO("sqlite:$location");
			$this->pdoConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch (PDOException $e) {
			throw new ScavengerDatabaseException($e->getMessage());
		}
	}
}