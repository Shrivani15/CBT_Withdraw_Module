<?php

require_once 'Account.php';
require_once 'Database.php';

abstract class BankingService
{
	/**
	 * Starts the requested banking service.
	 * @param Account $_account Account object
	 * @return void
	 */
	public function start(Account $_account)
	{
		$pin = (int) readline("Enter PIN: ");

		if (!$this->authenticate($_account, $pin)) {
			return;
		}

		$this->service($_account);
	}

	/**
	 * Used by API for authentication.
	 * @param Account $_account Account object
	 * @param int $_pin Account PIN
	 * @return bool
	 */
	public function login(Account $_account, int $_pin): bool
	{
		return $this->authenticate($_account, $_pin);
	}

	/**
	 * Authenticates the user by validating the PIN.
	 * Blocks the account after 3 failed attempts.
	 *
	 * @param Account $_account Account object
	 * @param int $_pin Entered PIN
	 * @return bool
	 */
	protected function authenticate(Account $_account, int $_pin) {
		if ($_account->getIsLocked()) {
			echo "Account Blocked\n";

			return false;
		}

		if (!array_key_exists("attempts", $_SESSION)) {
			$_SESSION["attempts"] = 3;

		}

		if ($_pin === $_account->getPin()) {

			$_SESSION["attempts"] = 3;

			return true;
		}

		$_SESSION["attempts"]--;

		if ($_SESSION["attempts"] > 0) {

			echo "Wrong PIN. Attempts Left : " . $_SESSION["attempts"] . "\n";

			return false;
		}

		$_account->setIsLocked();

		$account_repository = new AccountRepository(new Database());

		$account_repository->saveAccount($_account);

		$_SESSION["attempts"] = 3;

		echo "Account Blocked\n";

		return false;
	}

	/**
	 * Executes the selected banking service by child class implementation.
	 * @param Account $_accounts Account object
	 * @return void
	 */
	abstract public function service(Account $_accounts);
}
