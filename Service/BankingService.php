<?php

require_once __DIR__ . "/../Model/Account.php";
require_once __DIR__ . "/../Config/Database.php";
require_once __DIR__ . "/../Repository/AccountRepository.php";

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
	 * Authenticates the user.
	 * @param Account $_account
	 * @param int $_pin
	 * @return bool
	 */
	protected function authenticate(Account $_account, int $_pin): bool
	{
		if ($_account->getIsLocked()) {
			echo "Account Blocked\n";
			return false;
		}

		$account_repository = new AccountRepository(new Database());

		if ($_pin === $_account->getPin()) {

			$_account->setAttempts(3);

			$account_repository->saveAccount($_account);

			return true;
		}

		$remaining_attempts = $_account->getAttempts() - 1;

		$_account->setAttempts($remaining_attempts);

		if ($remaining_attempts == 0) {

			$_account->setIsLocked();

			$account_repository->saveAccount($_account);

			return false;
		}

		$account_repository->saveAccount($_account);

		return false;
	}
	/**
	 * Executes the selected banking service by child class implementation.
	 * @param Account $_accounts Account object
	 * @return void
	 */
	abstract public function service(Account $_accounts);
}
