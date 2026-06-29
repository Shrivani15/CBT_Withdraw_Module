<?php

class Database
{
	private mysqli $connection;

	/**
	 * Creates database connection.
	 * @return void
	 */
	public function __construct()
	{
		$this->connection = new mysqli("localhost", "root", "Shrivani_68", "banking");

		if ($this->connection->connect_error) {
			die("Connection Failed : " . $this->connection->connect_error);
		}
	}
    
    /**
     * Returns the database connection to repository
     * @return mysqli
     */
    public function getConnection()
    {
        return $this->connection;
    }
}