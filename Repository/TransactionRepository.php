<?php
date_default_timezone_set("Asia/Kolkata");
require_once __DIR__ . "/../Config/Database.php";
require_once __DIR__ . "/../Service/PreparedStatementTrait.php";
require_once __DIR__ . "/../Model/Account.php";
require_once __DIR__ . "/../Model/AccountType.php";

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
		$query = "SELECT 
					withdraw_amount, 
					balance_after,
					created_at 
				FROM 
					transactions 
				WHERE 
					account_id = ? 
				AND 
					DATE(created_at)=CURDATE()";

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
	 * @param ? $_account_id Account Number Stored ID
	 * @return array
	 */
	public function getTransactions(?int $_account_id = null){
		if ($_account_id === null) {

			$query = "SELECT
						a.account_number,
						a.user_name,
						at.account_type,
						a.phone_number,
						t.withdraw_amount,
						t.balance_after,
						t.created_at
					FROM 
						transactions As t
					INNER JOIN 
						accounts As a
					ON 
						t.account_id = a.id
					INNER JOIN 
						account_types As at
					ON 
						a.account_type_id = at.id
					ORDER BY 
						t.created_at 
					DESC";

			$statement = $this->connection->prepare($query);

		} else {

			$query = "SELECT
						a.account_number,
						a.user_name,
						at.account_type,
						a.phone_number,
						t.withdraw_amount,
						t.balance_after,
						t.created_at
					FROM 
						transactions As t
					INNER JOIN 
						accounts As a
					ON 
						t.account_id = a.id
					INNER JOIN 
						account_types As at
					ON 
						a.account_type_id = at.id
					WHERE
						t.account_id = ?
					ORDER BY 
						t.created_at 
					DESC";

			$statement = $this->executeStatement($query, "i", $_account_id);
		}

		if ($_account_id === null) {
			$statement->execute();
		}

		$result = $statement->get_result();

		$transactions = [];

		while ($row = $result->fetch_assoc()) {
			$transactions[] = $row;
		}

		$statement->close();

		return $transactions;
	}

	/**
	 * Returns total transactions for a particular date.
	 * @param ?string $_date Date (YYYY-MM-DD)
	 * @return array|null
	 */
	public function getTransactionCount(?string $_date = null)	{
		if ($_date === null) {
			$_date = date("Y-m-d");
		}

		$query = "SELECT
					DATE(created_at) AS transaction_date,
					COUNT(*) AS total_transactions
				FROM 
					transactions
				WHERE 
					DATE(created_at) = ?
				GROUP BY 
					DATE(created_at)";

		$statement = $this->executeStatement($query,"s",$_date);

		$result = $statement->get_result();

		$transaction = $result->fetch_assoc();

		$statement->close();

		return $transaction;
	}

	/**
	 * Saves a successful transaction.
	 * @param int $_account_id
	 * @param int $_amount
	 * @param int $_balance
	 * @return void
	 */
	public function saveTransaction(int $_account_id, int $_amount, int $_balance) {
		$query = "INSERT INTO 
					transactions(
						account_id, 
						withdraw_amount, 
						balance_after
					)
				VALUES 
					(?, ?, ?)";

		$statement = $this->executeStatement($query, "iii", $_account_id, $_amount, $_balance);

		$statement->close();
		}
}