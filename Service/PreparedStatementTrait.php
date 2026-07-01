<?php

trait PreparedStatementTrait
{

	/**
	 * Prepares, binds and executes the SQL query.
	 * @param string $_query SQL Query
	 * @param string $_types Parameter Types
	 * @param mixed ...$_values Values to bind
	 * @return mysqli_stmt
	 */
	protected function executeStatement(string $_query, string $_types, ...$_values) {

		$statement = $this->connection->prepare($_query);

		if ($statement === false) {
			die($this->connection->error);
		}

		$statement->bind_param($_types, ...$_values);

		$statement->execute();

		return $statement;
	}
}