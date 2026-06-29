<?php
date_default_timezone_set("Asia/Kolkata");
require_once "../Config/Database.php";
require_once "../Trait/PreparedStatementTrait.php";

class TransactionRepository
{
	use PreparedStatementTrait;
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
	 * @param int $_account_id Id consists of Account Details
	 * @return array
	 */
	public function getTodayTransactions(int $_account_id)
	{
		$query = "SELECT withdraw_amount, balance_after, created_at FROM transactions WHERE account_id = ? AND DATE(created_at)=CURDATE()";

		$statement = $this->executeStatement($query, "i", $_account_id);

		$result = $statement->get_result();

		$transactions = [];

		while ($row = $result->fetch_assoc()) {
			$transactions[] = $row;
		}

		$statement->close();

		return $transactions;
	}

	/**
	 * Returns transaction history.
	 * @param int $_account_id
	 * @return array
	 */
	public function getTransactions(int $_account_id) {
		$query = "SELECT withdraw_amount, balance_after, created_at FROM transactions WHERE account_id = ? ORDER BY created_at DESC";

		$statement = $this->executeStatement($query, "i", $_account_id);

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
	 * @param int $_account_id
	 * @param int $_amount
	 * @param int $_balance
	 * @return void
	 */
	public function saveTransaction(int $_account_id, int $_amount, int $_balance) {
		$query = "INSERT INTO transactions(account_id, withdraw_amount, balance_after) VALUES (?, ?, ?)";

		$statement = $this->executeStatement($query, "iii", $_account_id, $_amount, $_balance);

		$statement->close();
		}
}