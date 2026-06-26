<?php

require_once "Database.php";

class AccountRepository 
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
	 * Returns account object if account exists.
	 * @param int $_account_no Account Number
	 * @return Account|null
	 */
	public function getAccount(int $_account_no)
	{
		$query = "SELECT * FROM accounts WHERE account_number = ?";

		$statement = $this->connection->prepare($query);

		if ($statement === false) {
			die($this->connection->error);
		}

		$statement->bind_param("i", $_account_no);
		$statement->execute();

		$result = $statement->get_result();
		$account = $result->fetch_assoc();

		if ($account === null) {
			return null;
		}

		return new Account(
			$account["account_number"],
			$account["user_name"],
			$account["phone_number"],
			$account["account_type"],
			$account["pin"],
			$account["balance"],
			(bool)$account["is_locked"]
		);
	}

	/**
	 * Updates account details.
	 *
	 * @param Account $_account
	 * @return void
	 */
	public function saveAccount(Account $_account)
	{
		$query = "UPDATE accounts SET balance = ?, is_locked = ? WHERE account_number = ?";

		$balance = $_account->getBalance();
        $is_locked = $_account->getIsLocked() ? 1 : 0;
        $account_no = $_account->getAccountNumber();

        $statement = $this->connection->prepare($query);

		if ($statement === false) {
			die($this->connection->error);
		}

		$statement->bind_param("iii", $balance, $is_locked, $account_no);

		$statement->execute();
	}


}