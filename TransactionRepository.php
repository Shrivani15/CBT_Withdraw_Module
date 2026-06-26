<?php

require_once "Database.php";

class TransactionRepository
{
	private mysqli $connection;

	/**
	 * Constructor.
	 * @param Database $_database Database Object
	 * @return void
	 */
	public function __construct(Database $_database)
	{
		$this->connection = $_database->getConnection();
	}

	/**
	 * Returns today's transactions.
	 * @param int $_account_number Account Number
	 * @return array
	 */
	public function getTodayTransactions(int $_account_number)
	{
		$query = "SELECT withdraw_amount, balance_after, transaction_time FROM transactions WHERE account_number = ? AND DATE(transaction_time) = CURDATE()";

		$statement = $this->connection->prepare($query);

		if ($statement === false) {
			die($this->connection->error);
		}

		$statement->bind_param("i", $_account_number);

		$statement->execute();

		$result = $statement->get_result();

		$transactions = [];

		while ($row = $result->fetch_assoc()) {
			$transactions[] = $row;
		}

		$statement->close();

		return $transactions;
	}

	/**
	 * Saves a successful transaction.
	 *
	 * @param int $_account_number
	 * @param int $_amount
	 * @param int $_balance
	 * @return void
	 */
	public function saveTransaction(int $_account_number, int $_amount, int $_balance) {
		$query = "INSERT INTO transactions (account_number, withdraw_amount, balance_after, transaction_time) VALUES (?, ?, ?, NOW())";

		$statement = $this->connection->prepare($query);

		if ($statement === false) {
			die($this->connection->error);
		}

		$statement->bind_param("iii", $_account_number, $_amount, $_balance);

		$statement->execute();

		$statement->close();
	}
}