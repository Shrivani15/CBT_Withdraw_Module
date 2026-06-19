<?php

require_once 'Account.php';

abstract class BankingService
{
	/**
	 * Starts the requested banking service
	 * 
	 * @param Account $_accounts Account object
	 * @return void
	 */
	public function start(Account $_accounts) {
		if (!$this->authenticate($_accounts)) {
			return;
		}

		$this->service($_accounts);
	}

	/**
	 * Authenticates the user by validating the PIN.
	 * 
	 * @param Account $_account  Account object
	 * @return bool
	 */
	protected function authenticate(Account $_account) {
		$attempts = 3;

		while ($attempts > 0) {

			$pin = (int) readline("Enter PIN: ");

			if ($pin === $_account->getPin()) {
				return true;
			}

			$attempts--;

			if ($attempts > 0) {
				echo "Wrong PIN. Attempts Left: $attempts\n";
			}
		}

		$_account->setIsLocked();
		$_account->saveAccount();
		echo "Account Blocked\n";

		return false;
	}

	/**
	 * Executes the selected banking service by child class implementation.
	 * 
	 * @param Account $_accounts Account object
	 * @return void
	 */
	abstract public function service(Account $_accounts);
}
