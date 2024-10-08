<?php

namespace Scavenger\Internals\Bases;

use PDO;
use PDOException;
use PDOStatement;
use Scavenger\Internals\Exceptions\ScavengerDatabaseException;

class BaseDatabase
{
	protected PDO $pdoConnection;

	/**
	 * Executes a prepared SQL statement with the provided arguments and returns the result as an associative array.
	 *
	 * @param string $statement The SQL query string.
	 * @param array $args The parameters to bind to the query.
	 * @return array|null Returns the result set as an associative array.
	 *
	 * @throws ScavengerDatabaseException
	 */
	public function QuickQuery(string $statement, array $args = []): ?array
	{
		$statement = $this->PrepareStatement($statement);
		$statement->execute($args);
		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 * Prepaares a SQL PDO statement to be executed later.
	 * @param string $sql SQL for the statement.
	 * @return PDOStatement Returns the PDO statement.
	 *
	 * @throws ScavengerDatabaseException
	 */
	public function PrepareStatement(string $sql): PDOStatement
	{
		try {
			$statement = $this->pdoConnection->prepare($sql);

			if (!$statement) {
				throw new ScavengerDatabaseException("PrepareStatement failed: {$statement->errorCode()}");
			}
		} catch (PDOException $e) {
			throw new ScavengerDatabaseException("PrepareStatement failed: {$e->getMessage()}");
		}

		return $statement;
	}

	/**
	 * Executes a prepared SQL statement with the provided arguments and returns a single result.
	 *
	 * @param string $statement The SQL query string.
	 * @param array $args The parameters to bind to the query.
	 * @return array|null Returns the result as an associative array.
	 *
	 * @throws ScavengerDatabaseException
	 */
	public function QuickQuerySingle(string $statement, array $args = []): ?array
	{
		$statement = $this->PrepareStatement($statement);
		$statement->execute($args);
		return $statement->fetch(PDO::FETCH_ASSOC);
	}
}