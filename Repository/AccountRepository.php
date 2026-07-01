<?php

require_once __DIR__ . "/../Config/Database.php";
require_once __DIR__ . "/../Service/PreparedStatementTrait.php";
require_once __DIR__ . "/../Model/Account.php";
require_once __DIR__ . "/../Model/AccountType.php";

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
		$query = "SELECT
					a.id,
					a.account_number,
					a.user_name,
					a.phone_number,
					a.account_type_id,
					at.account_type,
					at.minimum_balance,
					at.maximum_limit,
					a.pin,
					a.balance,
					a.attempts,
					a.is_locked
				FROM
					accounts a
				INNER JOIN
					account_types at
				ON
					a.account_type_id = at.id
				WHERE
					a.account_number = ?";

		$statement = $this->executeStatement($query, "i", $_account_number);

		$result = $statement->get_result();

		$account = $result->fetch_assoc();

		$statement->close();

		if ($account === null) {
			return null;
		}
		$account_type = new AccountType($account["id"], $account["account_type"], $account["minimum_balance"],$account["maximum_limit"]);

		return new Account(
			$account["id"],
			$account["account_number"],
			$account["user_name"],
			$account["phone_number"],
			$account_type,
			$account["pin"],
			$account["balance"],
			$account["attempts"],
			(bool)$account["is_locked"]
		);
	}

	/**
	 * Returns account details with status.
	 * @param int $_account_number
	 * @return array|null
	 */
	public function getAccountStatus(int $_account_number) {
		$query = "SELECT
					a.account_number,
					a.user_name,
					at.account_type,
					a.phone_number,
					a.balance,
					CASE
						WHEN a.is_locked = 1
							THEN 'Blocked'
						ELSE
							'Active'
					END AS account_status
				FROM
					accounts a
				INNER JOIN
					account_types at
				ON
					a.account_type_id = at.id
				WHERE
					a.account_number = ?";

		$statement = $this->executeStatement($query, "i", $_account_number);

		$result = $statement->get_result();

		$account = $result->fetch_assoc();

		$statement->close();

		return $account;
	}

	/**
	 * Updates account details.
	 *
	 * @param Account $_account
	 * @return void
	 */
	public function saveAccount(Account $_account)
	{
		$query = "UPDATE
					accounts
				SET
					balance = ?,
					attempts = ?,
					is_locked = ?
				WHERE
					id = ?";

		$statement = $this->executeStatement($query, "iiii", $_account->getBalance(), $_account->getAttempts(), $_account->getIsLocked() ? 1 : 0, $_account->getId());

		$statement->close();
		}


}