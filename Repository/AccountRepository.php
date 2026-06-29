<?php

require_once "../Trait/PreparedStatementTrait.php";
require_once "../Config/Database.php";

class AccountRepository 
{
	use PreparedStatementTrait;
    private mysqli $connection;

	/**
	 * Constructor.
	 * @param Database $_database Database Object
	 * @return void
	 */
    public function __construct(Database $_database) {
        $this->connection = $_database->getConnection();
    }
    
	/**
	 * Returns account object if account exists.
	 * @param int $_account_number Account Number
	 * @return Account|null
	 */
	public function getAccount(int $_account_number) {
		$query = "SELECT * FROM accounts WHERE account_number = ?";

		$statement = $this->executeStatement($query, "i", $_account_number);

		$result = $statement->get_result();

		$account = $result->fetch_assoc();

		$statement->close();

		if ($account === null) {
			return null;
		}

		return new Account(
			$account["id"],
			$account["account_number"],
			$account["user_name"],
			$account["phone_number"],
			$account["account_type"],
			$account["pin"],
			$account["balance"],
			$account["attempts"],
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
		$query = "UPDATE accounts SET balance = ?, attempts = ?, is_locked = ? WHERE id = ?";

		$statement = $this->executeStatement($query, "iiii", $_account->getBalance(), $_account->getAttempts(), $_account->getIsLocked() ? 1 : 0, $_account->getId());

		$statement->close();
		}


}